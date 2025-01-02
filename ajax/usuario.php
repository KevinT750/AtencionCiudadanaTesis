<?php
session_start();
require_once "../model/Usuario.php";
require_once "../model/solicitud.php";
$usuario = new Usuario();
$solicitud = new ModeloSolicitud();

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

        case 'estado':
            if (isset($_SESSION['usu_id'])) {
                $est_id = $_SESSION['usu_id']; // ID del estudiante desde la sesión

                $rspta = $solicitud->estadoSolicitud($est_id);

                if ($rspta) {
                    echo json_encode([
                        'success' => true,
                        'solicitudes' => $rspta
                    ]);
                } else {
                    echo json_encode(['error' => 'No se encontraron solicitudes']);
                }
            } else {
                echo json_encode(['error' => 'No se encuentra la sesión activa']);
            }
            break;


        case 'verDocumento':
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                // Obtener la información del documento (id de Google Drive) desde la base de datos
                $documento = $solicitud->getDocumentoById($id);
                echo json_encode([
                    'fileId' => $documento['file_id'],  // ID del archivo en Google Drive
                    'fileType' => $documento['file_type']  // word o pdf
                ]);
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
