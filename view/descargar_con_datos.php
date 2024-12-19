<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require_once '../phpOffice/vendor/autoload.php';
require_once '../config/Conexion.php';
require_once '../Atencion_Ciudadana/Drive.php';

use PhpOffice\PhpWord\TemplateProcessor;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $nombre = $_POST['nombre'];
    $cedula = $_POST['cedula'];
    $carrera = $_POST['carrera']; // No se usa, pero queda si es necesario
    $telefono = $_POST['telefono'];
    $celular = $_POST['celular'];
    $correo = $_POST['correo'];
    $asuntoTexto = $_POST['asuntoTexto'];
    $archivo = $_FILES['archivo'];

    // Validar y mover archivo subido
    $uploadFile = null;
    if ($archivo['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $uploadFile = $uploadDir . basename($archivo['name']);
        move_uploaded_file($archivo['tmp_name'], $uploadFile);
    }

    // Generar documento usando plantilla
    $templatePath = realpath('C:/xampp/htdocs/Atencion Ciudadana/public/document/Solicitud Cambio.docx');
    $outputPath = "../public/document/Solicitud_Completada_$cedula.docx";

    if ($templatePath && file_exists($templatePath)) {
        $templateProcessor = new TemplateProcessor($templatePath);
        $templateProcessor->setValue('fecha', $fecha);
        $templateProcessor->setValue('nombres', $nombre);
        $templateProcessor->setValue('cedula', $cedula);
        $templateProcessor->setValue('telefono_domicilio', $telefono);
        $templateProcessor->setValue('celular', $celular);
        $templateProcessor->setValue('correo', $correo);
        $templateProcessor->setValue('asunto', $asuntoTexto);
        $templateProcessor->saveAs($outputPath);
    } else {
        die("Plantilla no encontrada o no accesible.");
    }

    // Interactuar con Google Drive
    $servicio = Drive::iniciarSesion();
    if (!$servicio) {
        die("Error al iniciar sesión en Google Drive.");
    }

    $carpetaAtencion = Drive::obtenerCarpetaAtencionCiudadana($servicio);
    if (!$carpetaAtencion['estado']) {
        die("Error al obtener carpeta de Atención Ciudadana: " . $carpetaAtencion['error']);
    }

    $carpetaMes = Drive::crearCarpetaAnioMes($carpetaAtencion['carpetaId'], $servicio);
    if (!$carpetaMes['estado']) {
        die("Error al crear carpeta de mes: " . $carpetaMes['error']);
    }

    $subida = Drive::subirArchivo(
        basename($outputPath),
        mime_content_type($outputPath),
        $outputPath,
        $carpetaMes['carpetaMesId'],
        $servicio
    );

    if (!$subida['estado']) {
        die("Error al subir archivo a Google Drive: " . $subida['error']);
    }

    // Verificar si la respuesta contiene la clave 'fileUrl'
    if (isset($subida['fileUrl'])) {
        echo "Archivo subido exitosamente a Google Drive. URL del archivo: " . $subida['fileUrl'];
    } else {
        die("La respuesta de la subida no contiene un enlace al archivo.");
    }
}
?>
