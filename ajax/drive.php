<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require_once '../Atencion_Ciudadana/drive.php';

$drive = new Drive();


// Verificar si el parámetro 'op' está presente en la URL
if (isset($_GET['op'])) {
    $op = $_GET['op'];

    switch ($op) {
        case 'ElimiarArchivos':
            // Obtener el servicio de Google Drive
            $resultadoServicio = Drive::servicioGoogle();
        
            if ($resultadoServicio["estado"]) {
                $servicio = $resultadoServicio["dato"];
                
                // Verificar que se haya recibido un ID de archivo
                if (isset($_POST['archivoId'])) {
                    $archivoId = $_POST['archivoId'];
        
                    // Llamar al método para eliminar el archivo
                    $resultadoEliminacion = Drive::eliminarArchivoPorId($archivoId, $servicio);
        
                    if ($resultadoEliminacion["estado"]) {
                        echo json_encode(["estado" => true, "mensaje" => $resultadoEliminacion["mensaje"]]);
                    } else {
                        echo json_encode(["estado" => false, "error" => $resultadoEliminacion["error"]]);
                    }
                } else {
                    echo json_encode(["estado" => false, "error" => "ID del archivo no proporcionado."]);
                }
            } else {
                echo json_encode(["estado" => false, "error" => $resultadoServicio["error"]]);
            }
        
            break;
        
        default:
            echo json_encode([
                'estado' => false,
                'error' => 'Operación no válida.'
            ]);
            break;
    }
} else {
    echo json_encode([
        'estado' => false,
        'error' => 'No se especificó la operación.'
    ]);
}
?>
