<?php
include 'api-google/vendor/autoload.php';

// Definir la constante RAIZ para la carpeta raíz de Google Drive
define("RAIZ", "1T9j6kxIHxsIWFMDajsc9IOUo01TQKC_3");

// Configurar la autenticación de Google API
putenv('GOOGLE_APPLICATION_CREDENTIALS=../Atencion_Ciudadana/practicadrive-445313-89dea3aae984.json');

class Drive {

    // Método para obtener el servicio de Google Drive
    static public function servicioGoogle(){
        try{
            $cliente = new Google_Client();
            $cliente->useApplicationDefaultCredentials();
            $cliente->setScopes(['https://www.googleapis.com/auth/drive.file']);
            $servicio = new Google_Service_Drive($cliente);
            return ["estado" => true, "dato" => $servicio];
        } catch (Google_Service_Exception $error) {   
            return ["estado" => false, "error" => $error->getMessage()];
        }
    }

    // Método para crear una carpeta
    static public function crearCarpeta($nombre, $carpetaPadre, $servicio){
        try{
            $carpeta = new Google_Service_Drive_DriveFile();
            $carpeta->setName($nombre);
            $carpeta->setParents([$carpetaPadre]);
            $carpeta->setDescription('Directorio creado por PHP API GOOGLE DRIVE');
            $carpeta->setMimeType('application/vnd.google-apps.folder');

            $parametros = [
                'fields' => 'id',
                'supportsAllDrives' => true,
            ];

            $nueva_carpeta = $servicio->files->create($carpeta, $parametros);           
            return ["estado" => true, "id" => $nueva_carpeta->id];
        } catch (Google_Service_Exception $error) {
            return ["estado" => false, "error" => $error->getMessage()];
        }
    }

    // Método para buscar una carpeta por nombre
    static public function buscarCarpetaPorNombre($nombreCarpeta, $carpetaPadre, $servicio) {
        try {
            $query = sprintf(
                "name='%s' and mimeType='application/vnd.google-apps.folder' and trashed=false and '%s' in parents",
                $nombreCarpeta,
                $carpetaPadre
            );
            $resultados = $servicio->files->listFiles([
                'q' => $query,
                'fields' => 'files(id, name)',
                'supportsAllDrives' => true
            ]);

            $carpetas = [];
            foreach ($resultados->getFiles() as $archivo) {
                $carpetas[] = ["id" => $archivo->getId(), "nombre" => $archivo->getName()];
            }

            if (count($carpetas) > 0) {
                return ["estado" => true, "carpetas" => $carpetas];
            } else {
                return ["estado" => false, "mensaje" => "No se encontraron carpetas con el nombre especificado."];
            }
        } catch (Google_Service_Exception $error) {
            return ["estado" => false, "error" => $error->getMessage()];
        }
    }

    // Método para verificar y crear una carpeta si no existe
    static public function verificarOCrearCarpeta($nombreCarpeta, $carpetaPadre, $servicio) {
        $resultadoBusqueda = self::buscarCarpetaPorNombre($nombreCarpeta, $carpetaPadre, $servicio);

        if ($resultadoBusqueda["estado"] && count($resultadoBusqueda["carpetas"]) > 0) {
            return ["estado" => true, "mensaje" => "La carpeta ya existe.", "id" => $resultadoBusqueda["carpetas"][0]["id"]];
        } else {
            $nuevaCarpeta = self::crearCarpeta($nombreCarpeta, $carpetaPadre, $servicio);
            if ($nuevaCarpeta["estado"]) {
                return ["estado" => true, "mensaje" => "Carpeta creada exitosamente.", "id" => $nuevaCarpeta["id"]];
            } else {
                return ["estado" => false, "error" => $nuevaCarpeta["error"]];
            }
        }
    }

    // Método para gestionar las carpetas anual y mensual
    static public function gestionarCarpetasAnualYMensual($carpetaRaiz, $servicio) {
        $anioActual = date("Y");
        $mesActual = date("F"); // Obtiene el nombre del mes actual en inglés

        // Verificar o crear carpeta del año actual
        $resultadoAnio = self::verificarOCrearCarpeta($anioActual, $carpetaRaiz, $servicio);

        if (!$resultadoAnio["estado"]) {
            return ["estado" => false, "error" => $resultadoAnio["error"]];
        }

        // Verificar o crear carpeta del mes actual dentro de la carpeta del año actual
        $resultadoMes = self::verificarOCrearCarpeta($mesActual, $resultadoAnio["id"], $servicio);

        if (!$resultadoMes["estado"]) {
            return ["estado" => false, "error" => $resultadoMes["error"]];
        }

        return [
            "estado" => true,
            "mensaje" => "Carpetas anual y mensual gestionadas exitosamente.",
            "anioCarpetaId" => $resultadoAnio["id"],
            "mesCarpetaId" => $resultadoMes["id"]
        ];
        
    }
    static public function visualizarArchivoPorId($archivoId, $servicio) {
        try {
            // Solicitar información sobre el archivo
            $archivo = $servicio->files->get($archivoId, [
                'fields' => 'id, name, mimeType, webViewLink', // Solicitar campos relevantes
                'supportsAllDrives' => true
            ]);
    
            // Verificar si se obtuvo la información del archivo
            if ($archivo) {
                // Comprobar si el archivo es un PDF o un documento de Google
                $isPdf = $archivo->getMimeType() === 'application/pdf';
                $isDoc = in_array($archivo->getMimeType(), ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword']);
        
                // Si es un PDF, crear un enlace específico para visualizarlo
                if ($isPdf || $isDoc) {
                    // Para .pdf o .doc, se usa la URL de vista pública
                    $enlacePublico = "https://drive.google.com/uc?export=view&id=" . $archivo->getId();
                } else {
                    // Para otros tipos de archivo, usar el enlace de visualización estándar
                    $enlacePublico = $archivo->getWebViewLink();
                }
        
                // Retornar la información del archivo, incluyendo el enlace de visualización
                return [
                    "estado" => true,
                    "archivo" => [
                        "id" => $archivo->getId(),
                        "nombre" => $archivo->getName(),
                        "enlace" => $enlacePublico // Enlace ajustado según tipo de archivo
                    ]
                ];
            } else {
                return [
                    "estado" => false,
                    "mensaje" => "No se encontró el archivo con el ID especificado."
                ];
            }
        } catch (Google_Service_Exception $error) {
            return [
                "estado" => false,
                "error" => $error->getMessage()
            ];
        }
    }
    
    
}




?>
