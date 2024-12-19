<?php
include 'api-google/vendor/autoload.php';


putenv('GOOGLE_APPLICATION_CREDENTIALS=../Atencion_Ciudadana/atencion-ciudadana-445118-8aa81e69186f.json');

class Drive {

    static public function iniciarSesion() {
        try {
            $cliente = new Google_Client();
            $cliente->useApplicationDefaultCredentials();
            $cliente->setScopes(['https://www.googleapis.com/auth/drive.file']);
            return new Google_Service_Drive($cliente);
        } catch (Exception $e) {
            return null;
        }
    }

    static public function obtenerCarpetaAtencionCiudadana($servicio) {
        $carpetaId = self::buscarCarpetaPorNombre("Atencion-Ciudadana", "root", $servicio);

        if (!$carpetaId) {
            $respuesta = self::crearCarpeta("Atencion-Ciudadana", "root", $servicio);
            if (!$respuesta['estado']) {
                return $respuesta;
            }
            $carpetaId = $respuesta['datos'];
        }

        return ['estado' => true, 'carpetaId' => $carpetaId];
    }

    // Drive.php

static public function crearCarpetaAnioMes($carpetaId, $servicio) {
    $anio = date("Y"); // Obtener el año actual
    $mes = date("m");  // Obtener el mes actual

    // Crear la carpeta para el año
    $carpetaAnio = new Google_Service_Drive_DriveFile();
    $carpetaAnio->setName($anio);  // Nombre de la carpeta con el año
    $carpetaAnio->setMimeType('application/vnd.google-apps.folder');
    $carpetaAnio->setParents([$carpetaId]); // Establecer la carpeta principal como su "padre"

    try {
        // Crear la carpeta del año en Google Drive
        $creadaAnio = $servicio->files->create($carpetaAnio);
        $carpetaIdAnio = $creadaAnio->id;

        // Ahora crear la carpeta del mes dentro de la carpeta del año
        $carpetaMes = new Google_Service_Drive_DriveFile();
        $carpetaMes->setName($mes); // Nombre de la carpeta con el mes
        $carpetaMes->setMimeType('application/vnd.google-apps.folder');
        $carpetaMes->setParents([$carpetaIdAnio]); // Establecer la carpeta del año como su "padre"

        // Crear la carpeta del mes en Google Drive
        $creadaMes = $servicio->files->create($carpetaMes);
        $carpetaIdMes = $creadaMes->id;

        // Devolver la carpeta de mes
        return ['estado' => true, 'carpetaMesId' => $carpetaIdMes];
    } catch (Exception $e) {
        return ['estado' => false, 'error' => $e->getMessage()];
    }
}


    static public function crearCarpeta($nombre, $carpetaPadre, $servicio) {
        try {
            $carpeta = new Google_Service_Drive_DriveFile();
            $carpeta->setName($nombre);
            $carpeta->setParents([$carpetaPadre]);
            $carpeta->setMimeType('application/vnd.google-apps.folder');

            $creada = $servicio->files->create($carpeta, ['fields' => 'id']);
            return ['estado' => true, 'datos' => $creada->id];
        } catch (Exception $e) {
            return ['estado' => false, 'error' => $e->getMessage()];
        }
    }

    static public function buscarCarpetaPorNombre($nombre, $carpetaPadre, $servicio) {
        $query = "'$carpetaPadre' in parents and name = '$nombre' and mimeType = 'application/vnd.google-apps.folder'";
        $resultados = $servicio->files->listFiles(['q' => $query, 'fields' => 'files(id)']);
        return count($resultados->files) ? $resultados->files[0]->id : null;
    }

    static public function subirArchivo($nombre, $tipo, $ruta, $carpetaId, $servicio) {
        try {
            $archivo = new Google_Service_Drive_DriveFile();
            $archivo->setName($nombre);
            $archivo->setParents([$carpetaId]);
    
            $contenido = file_get_contents($ruta);
            $creado = $servicio->files->create($archivo, [
                'data' => $contenido,
                'mimeType' => $tipo,
                'uploadType' => 'multipart',
            ]);
    
            // Obtener la URL del archivo subido
            $fileId = $creado->id;
            $file = $servicio->files->get($fileId, array("fields" => "webViewLink"));
            $fileUrl = $file->webViewLink;
    
            return ['estado' => true, 'fileId' => $fileId, 'fileUrl' => $fileUrl];
        } catch (Exception $e) {
            return ['estado' => false, 'error' => $e->getMessage()];
        }
    }
    
}
?>
