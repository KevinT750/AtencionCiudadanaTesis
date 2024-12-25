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

    // Ruta de la plantilla
    $templatePath = realpath('C:/xampp/htdocs/Atencion Ciudadana/public/document/Solicitud Cambio.docx');

    // Crear carpeta del mes actual si no existe
    $mesActual = date('F_Y'); // Ejemplo: December_2024
    $directorioMes = "../public/document/$mesActual";

    if (!is_dir($directorioMes)) {
        if (!mkdir($directorioMes, 0777, true)) {
            echo "<script>console.error('Error al crear directorio para el mes actual.');</script>";
            exit();
        }
    }

    // Generar el documento final en la carpeta del mes actual
    $outputPath = "$directorioMes/Solicitud_Completada_$cedula.docx";

    if ($templatePath && file_exists($templatePath)) {
        // Procesar plantilla
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
            $templateProcessor->saveAs($outputPath);

            // Subir archivo a Google Drive
            $servicio = Drive::servicioGoogle();
            if ($servicio['estado']) {
                $servicioDrive = $servicio['dato'];

                // Gestionar carpetas del año y mes actuales
                $resultadoCarpetas = Drive::gestionarCarpetasAnualYMensual(RAIZ, $servicioDrive);

                if ($resultadoCarpetas["estado"]) {
                    $anioCarpetaId = $resultadoCarpetas["anioCarpetaId"];
                    $mesCarpetaId = $resultadoCarpetas["mesCarpetaId"];

                    // Subir el archivo a la carpeta mensual
                    $archivoDrive = new Google_Service_Drive_DriveFile();
                    $archivoDrive->setName("{$nombre}_{$carrera}_{$fecha}.docx");
                    $archivoDrive->setParents([$mesCarpetaId]);

                    $contenidoArchivo = file_get_contents($outputPath);
                    $resultadoSubida = $servicioDrive->files->create($archivoDrive, [
                        'data' => $contenidoArchivo,
                        'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'uploadType' => 'multipart',
                        'fields' => 'id'
                    ]);

                    echo "<script>console.log('Archivo subido exitosamente con ID: " . $resultadoSubida->id . "');</script>";
                } else {
                    echo "<script>console.error('Error al gestionar carpetas: " . $resultadoCarpetas["error"] . "');</script>";
                }
            } else {
                echo "<script>console.error('Error al conectar con Google Drive: " . $servicio["error"] . "');</script>";
            }
        } catch (Exception $e) {
            echo "<script>console.error('Error al procesar la plantilla: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>console.error('Plantilla no encontrada o no accesible.');</script>";
    }
}
?>
