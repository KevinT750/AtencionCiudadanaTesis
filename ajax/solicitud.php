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
                                <button type="button" class="close" onclick="cerrarModal()">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p class="lead text-center">¿Qué acción desea realizar con la solicitud?</p>
                                <div id="botonesAccion" class="botones-accion">
                                    <button id="btnAprobar" class="btn btn-success">Aprobar</button>
                                    <button id="btnRechazar" class="btn btn-danger">Rechazar</button>
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
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const btnAprobar = document.getElementById("btnAprobar");
                            const btnRechazar = document.getElementById("btnRechazar");
                            const mensajeArea = document.getElementById("mensajeArea");
                            const btnEnviar = document.getElementById("btnEnviar");
            
                            btnAprobar.addEventListener("click", function () {
                                Swal.fire({
                                    title: "Solicitud Aprobada",
                                    text: "¿Estás seguro de proceder?",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonText: "Sí, proceder",
                                    cancelButtonText: "Cancelar",
                                }).then((result) => {
                                    mensajeArea.style.display = result.isConfirmed ? "block" : "none";
                                    if (result.isConfirmed) {
                                        // Cierra el modal actual
                                        cerrarModal();
                                        // Muestra el modalAprobar
                                        mostrarModalAprobar();
                                    }
                                });
                            });
            
                            btnRechazar.addEventListener("click", function () {
                                Swal.fire({
                                    title: "Solicitud Rechazada",
                                    text: "¿Estás seguro de proceder?",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonText: "Sí, proceder",
                                    cancelButtonText: "Cancelar",
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        mensajeArea.style.display = "none";
                                        Swal.fire("Solicitud rechazada", "La acción fue completada.", "success");
                                    }
                                });
                            });
            
                            btnEnviar.addEventListener("click", function () {
                                const mensaje = document.getElementById("mensaje").value.trim();
                                if (mensaje) {
                                    Swal.fire({ icon: "success", title: "Mensaje enviado", text: "El mensaje se ha enviado exitosamente." });
                                } else {
                                    Swal.fire({ icon: "error", title: "Error", text: "Por favor, escribe un mensaje antes de enviar." });
                                }
                            });
                        });
            
                        function cerrarModal() {
                            document.getElementById("modalSubir").style.display = "none";
                            document.getElementById("overlay").style.display = "none";
                        }
            
                        function mostrarModalAprobar() {
                            document.getElementById("modalEnviarSolicitud").style.display = "block";
                            document.getElementById("overlay").style.display = "block";
                        }
            
                        function cerrarModalAprobar() {
                            document.getElementById("modalEnviarSolicitud").style.display = "none";
                            document.getElementById("overlay").style.display = "none";
                        }
                    </script>
            
                    <style>
                        /* Estilos para el modal */
                        .modal {
                            display: none;
                            position: fixed;
                            z-index: 1;
                            left: 0;
                            top: 0;
                            width: 100%;
                            height: 100%;
                            background-color: rgba(0, 0, 0, 0.5);
                            justify-content: center;
                            align-items: center;
                        }
            
                        .modal-content {
                            background-color: white;
                            border-radius: 8px;
                            padding: 20px;
                            width: 50%;
                            max-width: 600px;
                            text-align: center;
                        }
            
                        .modal-header {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            background-color: #007bff;
                            color: white;
                            padding: 10px 20px;
                            border-radius: 5px;
                        }
            
                        .close {
                            font-size: 1.5rem;
                            background: none;
                            border: none;
                            color: white;
                            cursor: pointer;
                        }
            
                        .botones-accion {
                            display: flex;
                            justify-content: center;
                            gap: 20px;
                            margin-top: 20px;
                        }
            
                        .botones-accion .btn {
                            padding: 12px 30px;
                            font-size: 1rem;
                            border-radius: 5px;
                            cursor: pointer;
                            transition: background-color 0.3s ease;
                        }
            
                        .btn-success {
                            background-color: #28a745;
                            border: none;
                        }
            
                        .btn-danger {
                            background-color: #dc3545;
                            border: none;
                        }
            
                        .boton-enviar {
                            margin-top: 20px;
                            display: flex;
                            justify-content: center;
                        }
            
                        .form-control {
                            width: 100%;
                            padding: 10px;
                            border: 1px solid #ccc;
                            border-radius: 5px;
                            font-size: 1rem;
                        }
            
                        .overlay {
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background-color: rgba(0, 0, 0, 0.7);
                            display: none;
                        }
                    </style>';
                echo $model;
                break;
            
                case 'modalAprobar':
                    $modal = '
                    <div class="modal" id="modalEnviarSolicitud" tabindex="-1" aria-labelledby="modalEnviarSolicitudLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalEnviarSolicitudLabel">Enviar Solicitud a Coordinadores</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        <strong>Estudiante:</strong> <span id="nombreEstudiante">Juan Pérez</span>
                                    </div>
                
                                    <form id="formEnviarSolicitud">
                                        <div class="form-group">
                                            <label for="motivoSolicitud">Motivo de la Solicitud</label>
                                            <select class="form-control" id="motivoSolicitud" required>
                                                <option value="matricula">Matrícula</option>
                                                <option value="homologacion">Homologación</option>
                                                <option value="cambio_carrera">Cambio de Carrera</option>
                                            </select>
                                        </div>
                
                                        <div class="form-group">
                                            <label for="correoDestinatarios">Seleccionar Correo(s) de Coordinador(es)</label>
                                            <div id="correosDestinatarios" class="input-group">
                                                <input type="email" class="form-control" id="buscarCorreo" placeholder="Buscar correo..." required>
                                                <button class="btn" type="button" id="agregarCorreo">+</button>
                                            </div>
                                            <small id="errorCorreo" class="form-text text-danger" style="display: none;">Correo no válido. Por favor, ingresa un correo válido de los coordinadores.</small>
                                        </div>
                
                                        <div class="form-group">
                                            <label for="comentario">Comentario (opcional)</label>
                                            <textarea class="form-control" id="comentario" rows="4" placeholder="Deja un comentario si lo deseas..."></textarea>
                                        </div>
                
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <style>
                        .modal {
                            display: none;
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background-color: rgba(0, 0, 0, 0.5);
                            z-index: 999;
                        }
                        .modal-dialog {
                            margin-top: 10%;
                            margin-left: auto;
                            margin-right: auto;
                            width: 80%;
                        }
                        .modal-content {
                            background-color: white;
                            padding: 20px;
                            border-radius: 8px;
                        }
                        .modal-header {
                            background-color: #007bff;
                            color: white;
                            padding: 10px;
                        }
                        .modal-header .close {
                            color: white;
                            font-size: 1.5em;
                        }
                        .alert-info {
                            background-color: #d9edf7;
                            padding: 10px;
                            margin-bottom: 15px;
                            border-radius: 4px;
                        }
                        .form-group {
                            margin-bottom: 15px;
                        }
                        .form-control {
                            width: 100%;
                            padding: 10px;
                            border-radius: 4px;
                            border: 1px solid #ccc;
                        }
                        .input-group {
                            display: flex;
                            gap: 10px;
                        }
                        .input-group .btn {
                            background-color: #28a745;
                            color: white;
                            border: none;
                            cursor: pointer;
                        }
                        .input-group .btn:hover {
                            background-color: #218838;
                        }
                        .btn-primary {
                            background-color: #007bff;
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            cursor: pointer;
                        }
                        .btn-primary:hover {
                            background-color: #0056b3;
                        }
                    </style>
                
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const agregarCorreoBtn = document.getElementById("agregarCorreo");
                            const buscarCorreoInput = document.getElementById("buscarCorreo");
                            const correosDestinatariosDiv = document.getElementById("correosDestinatarios");
                            const formEnviarSolicitud = document.getElementById("formEnviarSolicitud");
                            const errorCorreo = document.getElementById("errorCorreo");
                
                            const correosDisponibles = [
                                "coordinador1@universidad.edu",
                                "coordinador2@universidad.edu",
                                "coordinador3@universidad.edu"
                            ];
                
                            agregarCorreoBtn.addEventListener("click", function () {
                                const correoSeleccionado = buscarCorreoInput.value.trim();
                
                                if (correoSeleccionado && correosDisponibles.includes(correoSeleccionado)) {
                                    const divCorreo = document.createElement("div");
                                    divCorreo.classList.add("input-group", "mb-2");
                                    divCorreo.innerHTML = `
                                        <input type="text" class="form-control" value="${correoSeleccionado}" disabled>
                                        <button class="btn btn-danger btn-sm" type="button" onclick="eliminarCorreo(this)">X</button>
                                    `;
                                    correosDestinatariosDiv.appendChild(divCorreo);
                                    buscarCorreoInput.value = "";
                                    errorCorreo.style.display = "none";
                                } else {
                                    errorCorreo.style.display = "block";
                                }
                            });
                
                            window.eliminarCorreo = function (button) {
                                button.parentElement.remove();
                            };
                
                            formEnviarSolicitud.addEventListener("submit", function (e) {
                                e.preventDefault();
                                const correosDestinatarios = [];
                                const comentarios = document.getElementById("comentario").value.trim();
                
                                const inputsCorreos = correosDestinatariosDiv.querySelectorAll("input");
                                inputsCorreos.forEach(input => {
                                    correosDestinatarios.push(input.value);
                                });
                
                                if (correosDestinatarios.length > 0) {
                                    alert(`La solicitud ha sido enviada a: ${correosDestinatarios.join(", ")}`);
                                    formEnviarSolicitud.reset();
                                    correosDestinatariosDiv.innerHTML = "";
                                } else {
                                    alert("Por favor, agrega al menos un correo destinatario.");
                                }
                            });
                        });
                    </script>
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
