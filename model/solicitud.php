<?php
require_once '../phpOffice/vendor/autoload.php';
require_once '../config/Conexion.php'; // Asegúrate de que la ruta sea correcta
require_once '../Atencion_Ciudadana/drive.php';

use PhpOffice\PhpWord\TemplateProcessor;

class ModeloSolicitud {
    public static function procesarSolicitud($datos, $archivo) {
        $servicio = Drive::servicioGoogle();
        if (!$servicio['estado']) {
            return ["estado" => false, "error" => $servicio["error"]];
        }
        $servicioDrive = $servicio['dato'];

        $resultadoCarpetas = Drive::gestionarCarpetasAnualYMensual(RAIZ, $servicioDrive);
        if (!$resultadoCarpetas["estado"]) {
            return ["estado" => false, "error" => $resultadoCarpetas["error"]];
        }
        $anioCarpetaId = $resultadoCarpetas["anioCarpetaId"];
        $mesCarpetaId = $resultadoCarpetas["mesCarpetaId"];

        $templatePath = realpath('C:/xampp/htdocs/Atencion Ciudadana/public/document/Solicitud Cambio.docx'); //Ruta absoluta
        if (!$templatePath || !file_exists($templatePath)) {
            return ["estado" => false, "error" => 'Plantilla no encontrada o no accesible.'];
        }

        try {
            $templateProcessor = new TemplateProcessor($templatePath);
            $templateProcessor->setValue('fecha', $datos['fecha']);
            $templateProcessor->setValue('nombres', $datos['nombre']);
            $templateProcessor->setValue('cedula', $datos['cedula']);
            $templateProcessor->setValue('carrera', $datos['carrera']);
            $templateProcessor->setValue('telefono_domicilio', $datos['telefono']);
            $templateProcessor->setValue('celular', $datos['celular']);
            $templateProcessor->setValue('correo', $datos['correo']);
            $templateProcessor->setValue('asunto', $datos['asuntoTexto']);

            $contentDoc = $templateProcessor->saveAs('php://output');

            $archivoDrive = new Google_Service_Drive_DriveFile();
            $archivoDrive->setName("{$datos['nombre']}_{$datos['carrera']}_{$datos['fecha']}.docx");
            $archivoDrive->setParents([$mesCarpetaId]);

            $resultadoSubida = $servicioDrive->files->create($archivoDrive, [
                'data' => $contentDoc,
                'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);

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

            return ["estado" => true, "doc_id" => $resultadoSubida->id, "cedula_id" => $resultadoCedulaSubida->id];

        } catch (Exception $e) {
            return ["estado" => false, "error" => $e->getMessage()];
        }
    }
}
?>