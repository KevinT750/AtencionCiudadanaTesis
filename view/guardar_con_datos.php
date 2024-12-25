<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    echo "<script>alert('No has iniciado sesión.');</script>";
    exit();
}

require_once '../phpOffice/vendor/autoload.php';
require_once '../config/Conexion.php';
require_once '../Atencion_Ciudadana/drive.php';

use PhpOffice\PhpWord\TemplateProcessor;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos del formulario
    $fecha = $_POST['fecha'];
    $nombre = $_POST['nombre'];
    $cedula = $_POST['cedula'];
    $carrera = $_POST['carrera'];
    $telefono = $_POST['telefono'];
    $celular = $_POST['celular'];
    $correo = $_POST['correo'];
    $asuntoTexto = $_POST['asuntoTexto'];
    $archivo = $_FILES['archivo'];

    // Verificar que el archivo no exceda los 2 MB
    if ($archivo['size'] > 2 * 1024 * 1024) {
        echo "<script>alert('El archivo excede el tamaño máximo permitido de 2 MB.');</script>";
        exit();
    }

    // Ruta de la plantilla
    $templatePath = realpath('C:/xampp/htdocs/Atencion Ciudadana/public/document/Solicitud Cambio.docx');

    // Subir el documento a Google Drive
    $servicio = Drive::servicioGoogle();
    if ($servicio['estado']) {
        $servicioDrive = $servicio['dato'];

        // Gestionar carpetas del año y mes actuales
        $resultadoCarpetas = Drive::gestionarCarpetasAnualYMensual(RAIZ, $servicioDrive);

        if ($resultadoCarpetas["estado"]) {
            $anioCarpetaId = $resultadoCarpetas["anioCarpetaId"];
            $mesCarpetaId = $resultadoCarpetas["mesCarpetaId"];

            // Procesar plantilla de solicitud y subirla a Google Drive
            if ($templatePath && file_exists($templatePath)) {
                try {
                    $templateProcessor = new TemplateProcessor($templatePath);
                    $templateProcessor->setValue('fecha', $fecha);
                    $templateProcessor->setValue('nombres', $nombre);
                    $templateProcessor->setValue('cedula', $cedula);
                    $templateProcessor->setValue('carrera', $carrera);
                    $templateProcessor->setValue('telefono_domicilio', $telefono);
                    $templateProcessor->setValue('celular', $celular);
                    $templateProcessor->setValue('correo', $correo);
                    $templateProcessor->setValue('asunto', $asuntoTexto);

                    // Guardar el archivo en memoria
                    $contentDoc = $templateProcessor->saveAs('php://output');

                    // Subir documento de solicitud a Google Drive
                    $archivoDrive = new Google_Service_Drive_DriveFile();
                    $archivoDrive->setName("{$nombre}_{$carrera}_{$fecha}.docx");
                    $archivoDrive->setParents([$mesCarpetaId]);

                    $resultadoSubida = $servicioDrive->files->create($archivoDrive, [
                        'data' => $contentDoc,
                        'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'uploadType' => 'multipart',
                        'fields' => 'id'
                    ]);

                    echo "<script>console.log('Documento subido exitosamente a Google Drive con ID: " . $resultadoSubida->id . "');</script>";

                    // Subir archivo de cédula a Google Drive
                    $archivoCedula = $archivo['tmp_name'];
                    $nombreCedula = "$cedula.pdf";
                    
                    $archivoCedulaDrive = new Google_Service_Drive_DriveFile();
                    $archivoCedulaDrive->setName($nombreCedula);
                    $archivoCedulaDrive->setParents([$mesCarpetaId]);

                    // Subir archivo PDF de cédula a Google Drive
                    $contenidoCedula = file_get_contents($archivoCedula);
                    $resultadoCedulaSubida = $servicioDrive->files->create($archivoCedulaDrive, [
                        'data' => $contenidoCedula,
                        'mimeType' => 'application/pdf',
                        'uploadType' => 'multipart',
                        'fields' => 'id'
                    ]);

                    echo "<script>console.log('Cédula subida exitosamente a Google Drive con ID: " . $resultadoCedulaSubida->id . "');</script>";

                } catch (Exception $e) {
                    echo "<script>console.error('Error al procesar la plantilla: " . $e->getMessage() . "');</script>";
                }
            } else {
                echo "<script>console.error('Plantilla no encontrada o no accesible.');</script>";
            }
        } else {
            echo "<script>console.error('Error al gestionar carpetas: " . $resultadoCarpetas["error"] . "');</script>";
        }
    } else {
        echo "<script>console.error('Error al conectar con Google Drive: " . $servicio["error"] . "');</script>";
    }
}
?>
