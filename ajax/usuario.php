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
            
            $clavehash = hash("SHA256", $clavea);
            
            $rspta = $usuario->verificar($logina, $clavehash);
            
            if ($rspta) {
                echo json_encode([
                    'success' => true
                ]);
            } else {
                echo json_encode(['error' => 'Usuario y/o Password incorrectos']);
            }
            break;

            case 'solicitud':
                // Verificar si la sesión está activa
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
    
                if (isset($_SESSION['doc_ids'], $_SESSION['cedula_ids'], $_SESSION['usu_id'])) {
                    $cedula_id = $_SESSION['cedula_ids'];
                    $doc_id = $_SESSION['doc_ids'];
                    $est_id = $_SESSION['usu_id'];
    
                    $rspta = $usuario->solicitud($est_id, $doc_id, $cedula_id);
    
                    if (isset($rspta['estado']) && $rspta['estado']) {
                        echo "Solicitud procesada correctamente.";
                    } else {
                        echo "Error: " . ($rspta['error'] ?? 'Respuesta no válida.');
                    }
                } else {
                    echo "No se encontraron IDs de documentos, cédulas o usuario en la sesión.";
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
