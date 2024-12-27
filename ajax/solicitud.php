<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require_once '../model/solicitud.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = $_POST;
    $archivo = $_FILES['archivo'];

    // Validar tamaño del archivo
    if ($archivo['size'] > 2 * 1024 * 1024) {
        echo json_encode([
            'estado' => false,
            'error' => 'El archivo excede el tamaño máximo permitido de 2 MB.'
        ]);
        exit();
    }

    // Procesar solicitud
    $resultado = ModeloSolicitud::procesarSolicitud($datos, $archivo);

    if ($resultado['estado']) {
        // Responder con éxito
        echo json_encode([
            'estado' => true,
            'mensaje' => 'Solicitud enviada correctamente.',
            'doc_id' => $resultado['doc_id'],
            'cedula_id' => $resultado['cedula_id']
        ]);
    } else {
        // Responder con error
        echo json_encode([
            'estado' => false,
            'error' => 'Error al enviar la solicitud: ' . $resultado['error']
        ]);
    }
}
?>
