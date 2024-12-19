<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require_once '../phpOffice/vendor/autoload.php';
require_once '../config/Conexion.php';
use PhpOffice\PhpWord\TemplateProcessor;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fecha = $_POST['fecha'];
    $nombre = $_POST['nombre'];
    $cedula = $_POST['cedula'];
    $carrera = $_POST['carrera'];
    $telefono = $_POST['telefono'];
    $celular = $_POST['celular'];
    $correo = $_POST['correo'];
    $asuntoTexto = $_POST['asuntoTexto'];
    $archivo = $_FILES['archivo'];

    // Procesar el archivo PDF (si es necesario)
    if ($archivo['error'] == 0) {
        // Ruta donde guardar el archivo PDF
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($archivo['name']);
        move_uploaded_file($archivo['tmp_name'], $uploadFile);
    }

    $templatePath = realpath('C:/xampp/htdocs/Atencion Ciudadana/public/document/Solicitud Cambio.docx');
    if ($templatePath === false) {
        echo "No se pudo encontrar el archivo de plantilla.";
        exit();
    }

    $outputPath = '../public/document/Solicitud_Completada_' . $cedula . '.docx';

    $templateProcessor = new TemplateProcessor($templatePath);

    $templateProcessor->setValue('fecha', $fecha);
    $templateProcessor->setValue('nombres', $nombre);
    $templateProcessor->setValue('cedula', $cedula);
    $templateProcessor->setValue('carrera', $carrera);
    $templateProcessor->setValue('telefono_domicilio', $telefono);
    $templateProcessor->setValue('celular', $celular);
    $templateProcessor->setValue('correo', $correo);
    $templateProcessor->setValue('asunto', $asuntoTexto);

    // Guardar el archivo completado
    $templateProcessor->saveAs($outputPath);

    // Redirigir al usuario al archivo generado (puedes ofrecerlo para descargar)
    header('Location: ' . $outputPath);  // Redirige al archivo generado
    exit();  // Detener la ejecución después de la redirección
}

ob_end_flush();
?>
