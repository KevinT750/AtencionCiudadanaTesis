<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require_once '../model/solicitud.php';
require_once '../model/Usuario.php';

$solicitud = new ModeloSolicitud();
$usuario = new Usuario();

// Verificar si el parámetro 'op' está presente en la URL
if (isset($_GET['op'])) {
    $op = $_GET['op'];

    switch ($op) {
        case 'estado':
            // Lógica para manejar la solicitud de estado
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
            break;
            case 'estado1':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Obtener los datos y los archivos
                    $datos = $_POST;
                    $archivoSolicitud = $_FILES['archivo_solicitud'];  // Solicitud
                    $archivoCedula = $_FILES['archivo_cedula'];        // Cédula
                
                    // Validar el tamaño del archivo de la solicitud
                    if ($archivoSolicitud['size'] > 2 * 1024 * 1024) {
                        echo json_encode([
                            'estado' => false,
                            'error' => 'El archivo de la solicitud excede el tamaño máximo permitido de 2 MB.'
                        ]);
                        exit();
                    }
            
                    // Validar el tamaño del archivo de la cédula
                    if ($archivoCedula['size'] > 2 * 1024 * 1024) {
                        echo json_encode([
                            'estado' => false,
                            'error' => 'El archivo de la cédula excede el tamaño máximo permitido de 2 MB.'
                        ]);
                        exit();
                    }
            
                    // Procesar solicitud
                    $resultado = ModeloSolicitud::procesarSolicitud1($datos, $archivoSolicitud, $archivoCedula);
                
                    if ($resultado['estado']) {
                        echo json_encode([
                            'estado' => true,
                            'mensaje' => 'Solicitud enviada correctamente.',
                            'doc_id' => $resultado['doc_id'],
                            'cedula_id' => $resultado['cedula_id']
                        ]);
                    } else {
                        echo json_encode([
                            'estado' => false,
                            'error' => 'Error al enviar la solicitud: ' . $resultado['error']
                        ]);
                    }
                }
                break;
            
            

        case 'Solicitudes':
            $rspta = $solicitud->Estado();
            $data = [];

            if($rspta !== false){
                while($reg = $rspta->fetch_row()){
                    $data[] = array(
                        "0" => $reg[1], //Fecha y Hora
                        "1" => strip_tags(html_entity_decode($reg[5])), //Nombre del Estudiante
                        "2" => strip_tags(html_entity_decode($reg[6])), //Correo Persssonal
                        "3" => strip_tags(html_entity_decode($reg[7])), //Correo Institucional
                        "4" => $reg[8], // Numero de Celular
                        "5" => strip_tags(html_entity_decode($reg[2])),
                        "6" => strip_tags(html_entity_decode($reg[3])),
                        "7" => $reg[4] //Estado
                    );
                }
                // Devuelve los datos en formato JSON para DataTables
                echo json_encode(array(
                    "sEcho" => 1, // Eco de la solicitud
                    "iTotalRecords" => count($data), // Total de registros encontrados
                    "iTotalDisplayRecords" => count($data), // Total de registros mostrados
                    "aaData" => $data // Los datos procesados
                ));
            }else{
                // En caso de error, responde con un mensaje adecuado
                echo json_encode(array(
                    "error" => "No se pudieron obtener los datos. Verifique la consulta o el procedimiento almacenado."
                ));
            }
            break;

        case 'Eliminar':
            // Verificar si los parámetros necesarios están presentes
            if (isset($_POST['sol_solicitud']) && isset($_POST['sol_documento'])) {
                $sol_solicitud = $_POST['sol_solicitud'];
                $sol_documento = $_POST['sol_documento'];
                // Llamar a la función eliminarSolicitud
                $rspta = $solicitud->eliminarSolicitud($sol_solicitud, $sol_documento);

                // Verificar la respuesta y retornar el resultado al cliente
                echo $rspta ? "Solicitud eliminada correctamente" : "No se pudo eliminar la solicitud.";
            } else {
                echo "Faltan parámetros para eliminar la solicitud.";
            }
            break;

            case 'modalSecretaria':
                if (session_status() == PHP_SESSION_NONE) {
                    session_start(); // Inicia la sesión si no está activa
                }
            
                // Almacenar variables en la sesión
                $_SESSION['columna2'] = isset($_POST['columna2']) ? $_POST['columna2'] : 'Sin valor';
                $_SESSION['columna3'] = isset($_POST['columna3']) ? $_POST['columna3'] : 'Sin valor';
            
                // Asegurarse de que el objeto usuario esté disponible
                if (isset($usuario)) {
                    $sol_solicitud = $_SESSION['columna2'];
                    $sol_documento = $_SESSION['columna3'];
            
                    // Obtener información del estudiante
                    $usu = $usuario->datosEst($sol_solicitud, $sol_documento);
            
                    if ($usu) {
                        $_SESSION['est_nombre'] = $usu['est_nombre'];
                        $_SESSION['est_correoPersonal'] = $usu['est_correoPersonal'];
                        $_SESSION['est_celular'] = $usu['est_celular'];
                    } else {
                        $_SESSION['est_nombre'] = 'Nombre no encontrado';
                    }
            
                    // Ahora puedes utilizar la variable de sesión 'est_nombre' en la vista
                    $model = '
                        <div class="modal" id="modalSubir">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Acción requerida</h5>
                                </div>
                                <div class="modal-body">
                                    <p class="lead text-center">¿Qué acción desea realizar con la solicitud?</p>
                                    <div id="botonesAccion" class="botones-accion">
                                        <button id="btnAprobar" class="btn btn-success">Aprobar</button>
                                        <button id="btnRechazar" class="btn btn-danger">Rechazar</button>
                                        <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cerrar</button> <!-- Nuevo botón de cerrar -->
                                    </div>
                                    <div id="mensajeArea" class="mensaje-area" style="display: none;">
                                        <label for="mensaje">Escriba un mensaje:</label>
                                        <textarea id="mensaje" class="form-control" rows="4" placeholder="Escribe tu mensaje aquí..."></textarea>
                                        <div class="boton-enviar">
                                            <button id="btnEnviar" class="btn btn-primary">Enviar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            
                        <div id="overlay" class="overlay"></div>
            
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                        <script src="../view/script/solicitudSe.js"></script>
            
                        <link rel="stylesheet" href="../public/css/solicitudSe.css">';
            
                    echo $model;
                } else {
                    echo "Error: El objeto \$model no está instanciado.";
                }
                break;
            
                case 'modalSol':
                    $sol_solicitud = $_POST['id'];
                    $modal = '
                    <!DOCTYPE html>
                    <html lang="es">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Visualizador de Documentos</title>
                        <link rel="stylesheet" href="../public/css/modalSol.css">
                    </head>
                    <body>
                        <div id="documentModal" class="modal">
                            <div id="overlay" class="modal-overlay" onclick="closeModal()"></div>
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5>Visualizar Documentos</h5>
                                    <button class="modal-close" onclick="closeModal()">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p class="lead text-center">Seleccione un documento para visualizar:</p>
                                    <div class="documento-iframe">
                                        <iframe 
                                            id="documentIframe"
                                            src="'.$sol_solicitud.'"
                                            allowfullscreen>
                                        </iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <script>
                            const modal = document.getElementById("documentModal");
                            const documentIframe = document.getElementById("documentIframe");
                
                            function openModal(documentUrl = null) {
                                if (documentUrl) {
                                    documentIframe.src = documentUrl;
                                }
                                modal.classList.add("show");
                            }
                
                            function closeModal() {
                                modal.classList.remove("show");
                                documentIframe.src = ""; // Reset iframe source
                            }
                
                            // Close modal with Escape key
                            document.addEventListener("keydown", (event) => {
                                if (event.key === "Escape" && modal.classList.contains("show")) {
                                    closeModal();
                                }
                            });
                
                            // Método para cargar el documento dinámicamente
                            window.loadDocument = function(url) {
                                openModal(url);
                            };
                        </script>
                    </body>
                    </html>
                    ';
                    echo json_encode(['modalContent' => $modal]);
                    break;
                
                
            
                case 'cerrarSesion':
                    unset($_SESSION['columna2']); // Destruir sesión columna2
                    unset($_SESSION['columna3']);
                    unset($_SESSION['est_nombre']); // Destruir sesión columna3
                    unset($_SESSION['est_correoPersonal']);
                    unset($_SESSION['est_celular']);
                    unset($_SESSION['doc_ids']);
                    unset($_SESSION['cedula_ids']);
                    echo 'Sesiones eliminadas correctamente';
                    break;

                case 'modalAprobar':
                    
                    
                    $nombre = isset($_SESSION['est_nombre']) ? $_SESSION['est_nombre'] : 'No disponible';
                    $rutaArchivo = '../Mailer/emails.json';
                    $correos = [];
                    if (file_exists($rutaArchivo)) {
                        $correos = json_decode(file_get_contents($rutaArchivo), true);
                    }
                
                    $correosSeleccionados = isset($_POST['correosSeleccionados']) ? $_POST['correosSeleccionados'] : [];
                
                    $modal = '
                    <div class="modal" id="modalEnviarSolicitud" tabindex="-1" aria-labelledby="modalEnviarSolicitudLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="alert alert-info">
                                                    <strong>Estudiante:</strong> <span id="nombreEstudiante"></span><br>
                                                    <strong>' . htmlspecialchars($nombre) . '</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <form id="formEnviarSolicitud" method="POST" action="">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="motivoSolicitud">Motivo de la Solicitud</label>
                                                        <select class="form-control" id="motivoSolicitud" name="motivoSolicitud" required>
                                                            <option value="matricula">Matrícula</option>
                                                            <option value="homologacion">Homologación</option>
                                                            <option value="cambio_carrera">Cambio de Carrera</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="correoDestinatarios">Seleccionar Correo(s) de Coordinador(es)</label>
                                                        <div id="correosDestinatarios">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <select id="comboCorreos" class="form-control me-2" name="correoSeleccionado">
                                                                    <option value="">Seleccione un correo...</option>';
                
                    // Mostrar los correos disponibles
                    foreach ($correos as $correo) {
                        $selected = in_array($correo, $correosSeleccionados) ? 'disabled' : '';
                        $modal .= "<option value='$correo' $selected>$correo</option>";
                    }
                
                    $modal .= '
                                                                </select>
                                                                <button class="btn btn-success" type="button" name="agregarCorreo">+</button>
                                                            </div>
                                                            <div id="contenedorCorreos">';
                
                    // Mostrar los correos seleccionados (si hay)
                    foreach ($correosSeleccionados as $correoSeleccionado) {
                        $modal .= "<div class='d-flex align-items-center mb-2'>$correoSeleccionado 
                                    <button type='button' class='btn btn-danger ms-2' name='eliminarCorreo' value='$correoSeleccionado'>X</button></div>";
                    }
                
                    $modal .= '
                                                            </div>
                                                            <small id="errorCorreo" class="form-text text-danger" style="display: none;">Correo ya seleccionado. Por favor, elige otro.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="comentario">Comentario (opcional)</label>
                                                        <textarea class="form-control" id="comentario" name="comentario" rows="4" placeholder="Deja un comentario si lo deseas..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                                                    <button type="button" class="btn btn-secondary" onclick="cerrarModalAprobar()">Cerrar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script src="../view/script/solicitudApro.js"></script>
                    <link rel="stylesheet" href="../public/css/solicitudApro.css">
                    ';
                    
                    echo $modal;
                    break;

                    case 'cambiarEstado':
                        if (session_status() == PHP_SESSION_NONE) {
                            session_start(); // Asegurar que la sesión esté activa
                        }
                    
                        // Verificar si las columnas están en la sesión, si no, obtenerlas del POST
                        if (!isset($_SESSION['columna2']) || !isset($_SESSION['columna3'])) {
                            if (isset($_POST['columna2']) && isset($_POST['columna3'])) {
                                $_SESSION['columna2'] = $_POST['columna2'];
                                $_SESSION['columna3'] = $_POST['columna3'];
                            } else {
                                echo json_encode([
                                    'success' => false,
                                    'message' => 'Error: Las columnas 2 y 3 no se encontraron en la sesión ni fueron enviadas mediante POST.'
                                ]);
                                break;
                            }
                        }
                    
                        // Asignar las columnas desde la sesión
                        $columna2 = $_SESSION['columna2'];
                        $columna3 = $_SESSION['columna3'];
                    
                        // Validar el ID del estado
                        $idEstado = isset($_POST['idEstado']) ? $_POST['idEstado'] : null;
                    
                        if ($idEstado !== null) {
                            if (isset($solicitud) && method_exists($solicitud, 'cambiarEstadoSolicitud')) {
                                $resultado = $solicitud->cambiarEstadoSolicitud($columna2, $columna3, $idEstado);
                    
                                echo json_encode([
                                    'success' => $resultado,
                                    'message' => $resultado ? 'Estado actualizado correctamente' : 'No se pudo actualizar el estado'
                                ]);
                            } else {
                                echo json_encode([
                                    'success' => false,
                                    'message' => 'Error: El objeto solicitud o su método cambiarEstadoSolicitud no existen.'
                                ]);
                            }
                        } else {
                            echo json_encode([
                                'success' => false,
                                'message' => 'Error: ID del estado no especificado.'
                            ]);
                        }
                        break;

                        case 'cambiarEstado1':
                            if (session_status() == PHP_SESSION_NONE) {
                                session_start(); // Asegura que la sesión esté activa
                            }
                        
                            // Validar la existencia de las variables en la sesión
                            if (!isset($_SESSION['columna2']) || !isset($_SESSION['columna3'])) {
                                echo json_encode([
                                    'success' => false,
                                    'message' => 'Error: Las columnas 2 y 3 no están disponibles en la sesión. Asegúrate de haber ejecutado modalSecretaria antes.'
                                ]);
                                break;
                            }
                        
                            $columna2 = $_SESSION['columna2'];
                            $columna3 = $_SESSION['columna3'];
                        
                            $idEstado = isset($_POST['id']) ? $_POST['id'] : null;
                        
                            if ($idEstado !== null) {
                                if (isset($solicitud) && method_exists($solicitud, 'cambiarEstadoSolicitud')) {
                                    $resultado = $solicitud->cambiarEstadoSolicitud($columna2, $columna3, $idEstado);
                        
                                    echo json_encode([
                                        'success' => $resultado,
                                        'message' => $resultado ? 'Estado actualizado correctamente' : 'No se pudo actualizar el estado'
                                    ]);
                                } else {
                                    echo json_encode([
                                        'success' => false,
                                        'message' => 'Error: El objeto solicitud o su método cambiarEstadoSolicitud no existen.'
                                    ]);
                                }
                            } else {
                                echo json_encode([
                                    'success' => false,
                                    'message' => 'Error: ID del estado no especificado.'
                                ]);
                            }
                            break;
                    
                    
        // Agregar otros casos si es necesario
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

