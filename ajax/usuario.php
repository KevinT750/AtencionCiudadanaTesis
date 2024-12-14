<?php
session_start();
require_once "../model/Usuario.php";

$usuario = new Usuario();

try {
    switch ($_GET["op"]) {
        case 'verificar':
            if (empty($_POST['logina']) || empty($_POST['clavea'])) {
                echo json_encode(['error' => 'Por favor complete todos los campos']);
                exit();
            }

            $logina = trim($_POST['logina']);
            $clavea = trim($_POST['clavea']);
            
            // Hasheamos la contraseña usando SHA-256
            $clavehash = hash("SHA256", $clavea);
            
            // Llamamos al método de verificar del modelo Usuario
            $rspta = $usuario->verificar($logina, $clavehash);
            
            if ($rspta) {
                echo json_encode([
                    'success' => true
                ]);
            } else {
                echo json_encode(['error' => 'Usuario y/o Password incorrectos']);
            }
            break;

        case 'salir':
            session_unset();
            session_destroy();
            header("Location: ../index.php");
            break;

        default:
            echo json_encode(['error' => 'Operación no válida']);
    }
} catch (Exception $e) {
    error_log("Error en usuario.php: " . $e->getMessage());
    echo json_encode([
        'error' => 'Error en el servidor',
        'message' => $e->getMessage()
    ]);
}
?>
