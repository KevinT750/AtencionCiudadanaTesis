<?php
session_start();
require_once "../model/Usuario.php";
require_once "../model/solicitud.php";
require_once "../Atencion_Ciudadana/drive.php";
$usuario = new Usuario();
$solicitud = new ModeloSolicitud();
$drive = new Drive();

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
            // Iniciar sesión si no está iniciada
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Verificar si las variables de sesión necesarias están disponibles
            if (!isset($_SESSION['doc_ids'], $_SESSION['cedula_ids'], $_SESSION['usu_id'])) {
                echo json_encode(['success' => false, 'error' => 'No se encontraron IDs de documentos, cédulas o usuario en la sesión.']);
                exit;
            }

            // Obtener los valores de sesión
            $cedula_id = $_SESSION['cedula_ids'];
            $doc_id = $_SESSION['doc_ids'];
            $est_id = $_SESSION['usu_id'];

            // Recibir y validar el estado_id desde POST (valor predeterminado 5 si no se envía)
            $estado_id = isset($_POST['estado_id']) && is_numeric($_POST['estado_id']) ? (int)$_POST['estado_id'] : 5;

            // Llamar a la función solicitud con los parámetros
            $rspta = $usuario->solicitud($est_id, $doc_id, $cedula_id, $estado_id);

            // Verificar la respuesta
            if (isset($rspta['estado']) && $rspta['estado']) {
                // Asignar la solicitud a la sesión con la clave 'sol_id'
                $_SESSION['sol_id'] = $rspta['sol_id'];

                echo json_encode([
                    'success' => true,
                    'message' => 'Solicitud procesada correctamente.',
                    'sol_id' => $rspta['sol_id']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => $rspta['error'] ?? 'Error desconocido al procesar la solicitud.'
                ]);
            }
            break;

        case 'solicitud1':
            // Verificar si las variables necesarias están presentes en la sesión y POST
            if (isset($_SESSION['doc_ids'], $_SESSION['cedula_ids']) && isset($_POST['est_id'], $_POST['estado_id'])) {
                $cedula_id = $_SESSION['cedula_ids'];
                $doc_id = $_SESSION['doc_ids'];
                $est_id = $_POST['est_id'];  // Usar el est_id que se recibe por POST
                $estado_id = $_POST['estado_id'];  // Usar el estado recibido por POST

                // Llamar a la función solicitud con los parámetros
                $rspta = $usuario->solicitud($est_id, $doc_id, $cedula_id, $estado_id);

                // Verificar la respuesta de la función solicitud
                if (isset($rspta['estado']) && $rspta['estado']) {
                    echo json_encode(['success' => true, 'message' => 'Solicitud procesada correctamente.']); // Respuesta JSON para éxito
                } else {
                    echo json_encode(['success' => false, 'error' => $rspta['error'] ?? 'Respuesta no válida.']); // Respuesta JSON para error
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'No se encontraron los datos necesarios en la sesión o en POST.']);
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

        case 'estadoSolicitud':
            if (isset($_SESSION['usu_id'])) {
                $usu_id = $_SESSION['usu_id']; // Obtener el ID del usuario desde la sesión
                $rspta = $solicitud->estadoSolicitud($usu_id); // Llama al método para obtener los datos
                $data = array();

                if ($rspta !== false) {
                    while ($reg = $rspta->fetch_row()) {
                        $estado = isset($reg[5]) ? $reg[5] : NULL; // Usa isset correctamente
                        $data[] = array(
                            "0" => $reg[1],  // ID de la solicitud
                            "1" => $reg[2],  // Fecha de la solicitud
                            "2" => strip_tags(html_entity_decode($reg[3])), // ID del documento de la solicitud
                            "3" => strip_tags(html_entity_decode($reg[4])), // ID del documento de cédula
                            "4" => $estado   // Estado de la solicitud
                        );
                    }

                    // Devuelve los datos en formato JSON para DataTables
                    echo json_encode(array(
                        "sEcho" => 1, // Eco de la solicitud
                        "iTotalRecords" => count($data), // Total de registros encontrados
                        "iTotalDisplayRecords" => count($data), // Total de registros mostrados
                        "aaData" => $data // Los datos procesados
                    ));
                } else {
                    // En caso de error, responde con un mensaje adecuado
                    echo json_encode(array(
                        "error" => "No se pudieron obtener los datos. Verifique la consulta o el procedimiento almacenado."
                    ));
                }
            } else {
                // Si la sesión no está activa, responde con un error
                echo json_encode(['error' => 'No se encuentra la sesión activa']);
            }
            break;



        case 'verDocumento':
            if (isset($_GET['id'])) {
                $id = $_GET['id'];

                // Obtener el servicio de Google Drive
                $servicio = $drive->servicioGoogle();

                if ($servicio['estado']) {
                    // Obtener el documento usando el ID
                    $documento = $drive->visualizarArchivoPorId($id, $servicio['dato']);

                    if ($documento['estado']) {
                        echo json_encode([
                            'estado' => true,
                            'archivo' => $documento['archivo']
                        ]);
                    } else {
                        echo json_encode([
                            'estado' => false,
                            'mensaje' => $documento['mensaje'] ?? $documento['error']
                        ]);
                    }
                } else {
                    echo json_encode([
                        'estado' => false,
                        'mensaje' => 'No se pudo obtener el servicio de Google Drive.'
                    ]);
                }
            }
            break;

        case 'verModal':
            $id_sol = $_POST['id_sol'] ?? '';

            // Validar que el ID no esté vacío
            if (empty($id_sol)) {
                die('ID de solicitud no proporcionado.');
            }

            // Generar el modal HTML
            $modal = '
                    
                    <!-- Modal para mostrar el archivo -->
                    <div class="modal fade" id="modalArchivo" tabindex="-1" aria-labelledby="modalArchivoLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalArchivoLabel">Ver Documento</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <iframe id="iframeDocumento" src="https://drive.google.com/file/d/' . $id_sol . '/preview" width="100%" height="500px" frameborder="0"></iframe>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                ';


            // Enviar el modal al cliente
            echo $modal;
            break;

        case 'estudianteId':
            if (isset($_POST['tipo']) && isset($_POST['valor'])) { // Validar si se recibieron los parámetros
                $tipo = trim($_POST['tipo']); // 'cedula' o 'nombre'
                $valor = trim($_POST['valor']); // Valor parcial a buscar
                $data = array(); // Almacenará los resultados

                // Llamar al método que ejecuta el procedimiento almacenado
                $rspta = $usuario->idEstudiante($tipo, $valor);

                // Verifica si se obtienen resultados y es un objeto
                if ($rspta !== false && is_object($rspta)) {
                    while ($reg = $rspta->fetch_assoc()) { // Ahora es seguro usar fetch_assoc()
                        $data[] = array(
                            'id' => $reg['est_id'], // ID del estudiante
                            'nombre' => $reg['est_nombre'], // Nombre del estudiante
                            'cedula' => $reg['est_cedula'] // Cédula del estudiante
                        );
                    }
                    // Enviar los resultados como JSON
                    echo json_encode($data);
                } else if (is_array($rspta)) {
                    // Si rspta es un array, asumir que ya contiene los datos y responder
                    echo json_encode($rspta);
                } else {
                    // Si no hay resultados, responder con un error
                    echo json_encode(array(
                        'error' => true,
                        'message' => 'No se encontraron resultados para la búsqueda.'
                    ));
                }
            } else {
                // Respuesta en caso de error si los parámetros no fueron proporcionados
                echo json_encode(array(
                    'error' => true,
                    'message' => 'Parámetros tipo y valor son requeridos.'
                ));
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
