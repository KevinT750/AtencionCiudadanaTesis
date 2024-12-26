<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    echo "<script>alert('No has iniciado sesión.'); window.location.href='../index.php';</script>"; // Redirige al index
    exit();
}

require_once 'modelo_solicitud.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = $_POST;
    $archivo = $_FILES['archivo'];

    if ($archivo['size'] > 2 * 1024 * 1024) {
        echo "<script>alert('El archivo excede el tamaño máximo permitido de 2 MB.'); window.location.href='formulario.php';</script>";
        exit();
    }

    $resultado = ModeloSolicitud::procesarSolicitud($datos, $archivo);

    if ($resultado['estado']) {
        echo "<script>console.log('Documento subido exitosamente a Google Drive con ID: " . $resultado['doc_id'] . " y Cédula con ID: " . $resultado['cedula_id'] . "'); alert('Solicitud enviada correctamente.'); window.location.href='formulario.php';</script>";
    } else {
        echo "<script>console.error('Error: " . $resultado['error'] . "'); alert('Error al enviar la solicitud: " . $resultado['error'] . "'); window.location.href='formulario.php';</script>";
    }
}
?>