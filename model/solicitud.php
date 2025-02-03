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
    
    public static function procesarSolicitud1($datos, $archivoSolicitud, $archivoCedula) {
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
            // Procesar plantilla Word con solo nombre y cédula
            $templateProcessor = new TemplateProcessor($templatePath);
            $templateProcessor->setValue('nombres', $datos['nombre']);
            $templateProcessor->setValue('cedula', $datos['cedula']);
    
            // Crear documento temporal en memoria
            $tempFile = tempnam(sys_get_temp_dir(), 'Solicitud_') . '.docx';
            $templateProcessor->saveAs($tempFile);
    
            // Subir el documento a Google Drive con el nombre basado en 'nombre' y 'cedula'
            $nombreSolicitud = "{$datos['nombre']}_{$datos['cedula']}_Solicitud.docx";
            $archivoSolicitudDrive = new Google_Service_Drive_DriveFile();
            $archivoSolicitudDrive->setName($nombreSolicitud);
            $archivoSolicitudDrive->setParents([$mesCarpetaId]);
    
            $fileContent = file_get_contents($tempFile);
            $resultadoSolicitudSubida = $servicioDrive->files->create($archivoSolicitudDrive, [
                'data' => $fileContent,
                'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);
            unlink($tempFile); // Eliminar archivo temporal
    
            // Establecer el archivo como público
            $fileId = $resultadoSolicitudSubida->id;
            $permiso = new Google_Service_Drive_Permission();
            $permiso->setType('anyone');
            $permiso->setRole('reader');
            $servicioDrive->permissions->create($fileId, $permiso);
    
            // Subir archivo PDF de la cédula con el nombre basado en 'cedula'
            $archivoCedulaTemp = $archivoCedula['tmp_name'];
            $nombreCedula = "{$datos['cedula']}_Cedula.pdf";
    
            $archivoCedulaDrive = new Google_Service_Drive_DriveFile();
            $archivoCedulaDrive->setName($nombreCedula);
            $archivoCedulaDrive->setParents([$mesCarpetaId]);
    
            $contenidoCedula = file_get_contents($archivoCedulaTemp);
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
            $_SESSION['doc_ids'] = $resultadoSolicitudSubida->id;  // Guardar el ID de la solicitud
            $_SESSION['cedula_ids'] = $fileCedulaId;  // Guardar el ID de la cédula
    
            // Retornar las IDs de los archivos subidos
            return [
                "estado" => true,
                "doc_id" => $resultadoSolicitudSubida->id,
                "cedula_id" => $fileCedulaId
            ];
    
        } catch (Exception $e) {
            return ["estado" => false, "error" => 'Error al procesar la solicitud: ' . $e->getMessage()];
        }
    }
    
    
    public function estadoSolicitud($usu_id) {
        $op = 1;
        $sql = "CALL SP_GetSolicitudesEstId($op, $usu_id)";
        return ejecutarConsulta($sql); 
    }

    public function Estado(){
        $sql = "call atencion_ciudadana_ist17j.SP_MOSTRAR_SOLICITUDES()";
        return ejecutarConsulta($sql);
    }
    
     public function eliminarSolicitud($sol_solicitud, $sol_documento){
        $sql = "call atencion_ciudadana_ist17j.SP_EliminarSolicitud('$sol_solicitud', '$sol_documento');";
        return ejecutarConsulta($sql);
    }

    public function cambiarEstadoSolicitud($sol_solicitud, $sol_documento, $idEstado){
        $sql = "call atencion_ciudadana_ist17j.SP_ActualizarEstadoSolicitud('$sol_solicitud', '$sol_documento', '$idEstado');
        ";
        return ejecutarConsulta($sql);
    }

    public function insertSeguimiento($op, $sol_id, $est_id, $seg_accion, $seg_comentario, $seg_visto) {
        // Asegurarse de que el SQL esté bien formado
        $sql = "CALL atencion_ciudadana_ist17j.SP_Seguimiento('$op', '$sol_id', '$est_id', '$seg_accion', '$seg_comentario', '$seg_visto')";
        return ejecutarConsulta($sql);
    }
    
    public function mostrarSeguimiento($op, $sol_id) {
        // Asegurarse de que el SQL esté bien formado
        $sql = "CALL atencion_ciudadana_ist17j.SP_Seguimiento('$op', '$sol_id')";
        return ejecutarConsulta($sql);
    }
}
?>
