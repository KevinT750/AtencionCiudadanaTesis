<?php
require_once '../phpOffice/vendor/autoload.php';
require_once '../config/Conexion.php'; // Verifica que la ruta sea correcta
require_once '../Atencion_Ciudadana/drive.php';

use PhpOffice\PhpWord\TemplateProcessor;

class ModeloSolicitud {
    public static function procesarSolicitud($datos, $archivo) {
        // Inicializar el servicio de Google Drive
        $servicio = Drive::servicioGoogle();
        if (!$servicio['estado']) {
            return ["estado" => false, "error" => $servicio["error"]];
        }
        $servicioDrive = $servicio['dato'];
    
        // Gestionar carpetas en Google Drive por año y mes
        $resultadoCarpetas = Drive::gestionarCarpetasAnualYMensual(RAIZ, $servicioDrive);
        if (!$resultadoCarpetas["estado"]) {
            return ["estado" => false, "error" => $resultadoCarpetas["error"]];
        }
        $anioCarpetaId = $resultadoCarpetas["anioCarpetaId"];
        $mesCarpetaId = $resultadoCarpetas["mesCarpetaId"];
    
        // Ruta absoluta a la plantilla del documento Word
        $templatePath = realpath('C:/xampp/htdocs/Atencion Ciudadana/public/document/Solicitud Cambio.docx');
        if (!$templatePath || !file_exists($templatePath)) {
            return ["estado" => false, "error" => 'Plantilla no encontrada o no accesible.'];
        }
    
        try {
            // Procesar plantilla Word
            $templateProcessor = new TemplateProcessor($templatePath);
            $templateProcessor->setValue('fecha', $datos['fecha']);
            $templateProcessor->setValue('nombres', $datos['nombre']);
            $templateProcessor->setValue('cedula', $datos['cedula']);
            $templateProcessor->setValue('carrera', $datos['carrera']);
            $templateProcessor->setValue('telefono_domicilio', $datos['telefono']);
            $templateProcessor->setValue('celular', $datos['celular']);
            $templateProcessor->setValue('correo', $datos['correo']);
            $templateProcessor->setValue('asunto', $datos['asuntoTexto']);
    
            // Crear documento temporal en memoria
            $tempFile = tempnam(sys_get_temp_dir(), 'Solicitud_') . '.docx';
            $templateProcessor->saveAs($tempFile);
    
            // Subir el documento a Google Drive
            $archivoDrive = new Google_Service_Drive_DriveFile();
            $archivoDrive->setName("{$datos['nombre']}_{$datos['carrera']}_{$datos['fecha']}.docx");
            $archivoDrive->setParents([$mesCarpetaId]);
    
            $fileContent = file_get_contents($tempFile);
            $resultadoSubida = $servicioDrive->files->create($archivoDrive, [
                'data' => $fileContent,
                'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);
            unlink($tempFile); // Eliminar archivo temporal
    
            // Establecer el archivo como público
            $fileId = $resultadoSubida->id;
            $permiso = new Google_Service_Drive_Permission();
            $permiso->setType('anyone');
            $permiso->setRole('reader');
            $servicioDrive->permissions->create($fileId, $permiso);
    
            // Subir archivo PDF de cédula
            $archivoCedula = $archivo['tmp_name'];
            $nombreCedula = "{$datos['cedula']}.pdf";
    
            $archivoCedulaDrive = new Google_Service_Drive_DriveFile();
            $archivoCedulaDrive->setName($nombreCedula);
            $archivoCedulaDrive->setParents([$mesCarpetaId]);
    
            $contenidoCedula = file_get_contents($archivoCedula);
            $resultadoCedulaSubida = $servicioDrive->files->create($archivoCedulaDrive, [
                'data' => $contenidoCedula,
                'mimeType' => 'application/pdf',
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);
    
            // Establecer el archivo de cédula como público
            $fileCedulaId = $resultadoCedulaSubida->id;
            $permisoCedula = new Google_Service_Drive_Permission();
            $permisoCedula->setType('anyone');
            $permisoCedula->setRole('reader');
            $servicioDrive->permissions->create($fileCedulaId, $permisoCedula);
    
            // Guardar las IDs en la sesión, sobrescribiendo los valores anteriores
            $_SESSION['doc_ids'] = $resultadoSubida->id; // Sobrescribir con el último ID
            $_SESSION['cedula_ids'] = $resultadoCedulaSubida->id; // Sobrescribir con el último ID
    
            // Retornar las IDs de los archivos subidos
            return [
                "estado" => true,
                "doc_id" => $resultadoSubida->id,
                "cedula_id" => $resultadoCedulaSubida->id
            ];
    
        } catch (Exception $e) {
            return ["estado" => false, "error" => 'Error al procesar la solicitud: ' . $e->getMessage()];
        }
    }
    
    public function estadoSolicitud($usu_id) {
        $sql = "CALL SP_GetSolicitudesEstId($usu_id)";
        return ejecutarConsulta($sql); // Asegúrate de que `$this->conn` es una instancia válida de mysqli.
    }
    
    

}
?>
