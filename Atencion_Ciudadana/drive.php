<?php
include 'api-google/vendor/autoload.php';

define("RAIZ", "1T9j6kxIHxsIWFMDajsc9IOUo01TQKC_3"); 
putenv('GOOGLE_APPLICATION_CREDENTIALS=../Atencion_Ciudadana/practicadrive-445313-89dea3aae984.json');

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


    static public function crearCarpetaAnioMes($RAIZ, $servicio) {
        $anio = date("Y"); // Obtener el año actual
        $mes = date("m");  // Obtener el mes actual

        // Crear la carpeta para el año
        $carpetaAnio = new Google_Service_Drive_DriveFile();
        $carpetaAnio->setName($anio);  // Nombre de la carpeta con el año
        $carpetaAnio->setMimeType('application/vnd.google-apps.folder');
        $carpetaAnio->setParents(["1T9j6kxIHxsIWFMDajsc9IOUo01TQKC_3"]); // Establecer la carpeta principal como su "padre"

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

    function guardarArchivo($nombreCompleto, $fecha, $carrera, $archivoTemp, $rutaBase = 'uploads') {
        // Obtener el año y el mes de la fecha
        $anio = date('Y', strtotime($fecha));
        $mes = date('m', strtotime($fecha));
        
        // Crear las carpetas de año y mes si no existen
        $rutaAnio = $rutaBase . DIRECTORY_SEPARATOR . $anio;
        $rutaMes = $rutaAnio . DIRECTORY_SEPARATOR . $mes;
        
        if (!is_dir($rutaAnio)) {
            mkdir($rutaAnio, 0777, true);
        }
        if (!is_dir($rutaMes)) {
            mkdir($rutaMes, 0777, true);
        }
        
        // Formato del nombre del archivo
        $baseNombre = $nombreCompleto . '_' . $fecha . '_' . $carrera;
        $nombreArchivo = $baseNombre . '.doc';
        $rutaArchivo = $rutaMes . DIRECTORY_SEPARATOR . $nombreArchivo;
        
        // Verificar si el archivo existe y generar un nombre único
        $contador = 1;
        while (file_exists($rutaArchivo)) {
            $nombreArchivo = $baseNombre . '_' . $contador . '.doc';
            $rutaArchivo = $rutaMes . DIRECTORY_SEPARATOR . $nombreArchivo;
            $contador++;
        }
        
        // Mover el archivo temporal a la ruta generada
        if (move_uploaded_file($archivoTemp, $rutaArchivo)) {
            echo "Archivo guardado exitosamente: $rutaArchivo";
            return $rutaArchivo;
        } else {
            echo "Error al guardar el archivo.";
            return false;
        }
    }
}
?>
