<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require_once '../model/solicitud.php';

$solicitud = new ModeloSolicitud();

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
                break;
            
                case 'modalAprobar':
                    // Cargar los correos desde el archivo JSON
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
                                                    <strong>Estudiante:</strong> <span id="nombreEstudiante">Juan Pérez</span>
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

