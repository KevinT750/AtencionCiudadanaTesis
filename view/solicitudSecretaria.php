<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require 'header.php';

if ($_SESSION['Ver_Solicitudes'] == 1) {
?>
    <link rel="stylesheet" href="../public/css/solicitudSecretaria.css">

    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- Botones para gestionar solicitudes y correos -->
                    <div class="mb-4">
                        <?php if ($_SESSION['Subir_Solicitud'] == 1) { ?>
                            <button id="btnMostrarFormulario" class="btn btn-success btn-lg me-3">
                                <i class="fa fa-plus-circle"></i> Subir Solicitud
                            </button>
                        <?php } ?>
                        <button id="btnMostrarCrud" class="btn btn-info btn-lg">
                            <i class="fa fa-envelope"></i> Gestionar Correos
                        </button>
                    </div>

                    <!-- Formulario de solicitud -->
                    <!-- Formulario para subir solicitud -->
                    <div id="formularioSolicitud" class="box" style="display: none;">
                        <div class="box-header with-border text-center">
                            <h1 class="box-title">
                                <i class="fa fa-upload"></i> Formulario de Subida de Documento
                            </h1>
                        </div>
                        <div class="box-body">
                            <form id="formSubirSolicitud" method="post" enctype="multipart/form-data">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="nombre">Nombres Completos:</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese sus nombres completos" required autocomplete="off">
                                        <ul id="resultados-nombre" class="list-group"></ul> <!-- Contenedor de resultados -->
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="cedula">Número de Cédula:</label>
                                        <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Ingrese su número de cédula" required autocomplete="off">
                                        <ul id="resultados-cedula" class="list-group"></ul> <!-- Contenedor de resultados -->
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="archivo_solicitud">Subir Solicitud (PDF o DOC):</label>
                                        <input type="file" class="form-control-file" id="archivo_solicitud" name="archivo_solicitud" accept=".pdf, .doc, .docx" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="archivo_cedula">Subir Cédula Escaneada (PDF):</label>
                                        <input type="file" class="form-control-file" id="archivo_cedula" name="archivo_cedula" accept=".pdf" required>
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa fa-upload"></i> Subir Solicitud
                                    </button>
                                    <button type="reset" class="btn btn-danger btn-lg mx-2">
                                        <i class="fa fa-trash"></i> Limpiar
                                    </button>
                                    <button type="button" id="btnCancelar" class="btn btn-secondary btn-lg mx-2">
                                        <i class="fa fa-times"></i> Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php  ?>

                    <!-- Tabla de correos -->
                    <div class="card shadow-lg" id="tablaCorreos" style="display:none;">
                        <div class="card-header bg-primary text-white text-center">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-envelope me-2"></i> Gestión de Correos
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tablaCorreosList" class="table table-hover table-striped table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Correo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaCorreosBody">
                                        <!-- Los correos se insertarán aquí dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                            <button id="btnNuevoCorreo" class="btn btn-primary btn-lg">
                                <i class="fa fa-plus"></i> Crear Nuevo Correo
                            </button>
                            <button id="btnCerrarTabla" class="btn btn-danger btn-lg mt-3">
                                <i class="fa fa-times"></i> Cerrar
                            </button>
                        </div>
                    </div>

                    <!-- Modal para Crear o Editar Correo -->
                    <div id="modalCorreo" class="modal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalTitulo">Crear Correo</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="formCorreo">
                                        <div class="form-group">
                                            <label for="correo">Correo:</label>
                                            <input type="email" class="form-control" id="correo" name="correo" required>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-primary" id="btnGuardarCorreo">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de solicitudes -->
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i> Gestión de Solicitudes
                    </h3>
                </div>
                <div class="card-body">
                    <div id="filter-buttons" class="mb-3">
                        <!-- Los botones de filtrado se insertarán aquí -->
                    </div>
                    <div class="table-responsive">
                        <table id="solicitudesSecret" class="table table-hover table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Nombre</th>
                                    <th>Correo Personal</th>
                                    <th>Correo Institucional</th>
                                    <th>Número de Celular</th>
                                    <th>Solicitud</th>
                                    <th>Documento</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Las solicitudes se insertarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php
} else {
    require 'noacceso.php';
}

require 'footer.php';
?>

<script src="script/solicitudSecretariaCop.js"></script>
<script src="script/solicitudSecretaria.js"></script>
<script>
    // Mostrar formulario y tabla de correos con Bootstrap
    // Mostrar el formulario al hacer clic en el botón
    document.getElementById('btnMostrarFormulario').addEventListener('click', function() {
        document.getElementById('formularioSolicitud').style.display = 'block';
    });

    // Cancelar y ocultar el formulario al hacer clic en el botón de cancelar
    document.getElementById('btnCancelar').addEventListener('click', function() {
        document.getElementById('formularioSolicitud').style.display = 'none';
    });

    // Mostrar tabla de correos
    document.getElementById('btnMostrarCrud').addEventListener('click', function() {
        document.getElementById('tablaCorreos').style.display = 'block';
        cargarTablaCorreos();
    });

    // Función para cargar la tabla de correos
    function cargarTablaCorreos() {
        fetch('../ajax/solicitud.php?op=getEmails')
            .then(response => response.json())
            .then(data => {
                const tablaBody = document.getElementById('tablaCorreosBody');
                tablaBody.innerHTML = '';
                data.forEach((correo, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td>${correo}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editarCorreo(${index}, '${correo}')">Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarCorreo(${index})">Eliminar</button>
                    </td>
                `;
                    tablaBody.appendChild(row);
                });
            })
            .catch(error => console.log(error));
    }

    // Funciones para agregar, editar y eliminar correos
    document.getElementById('btnNuevoCorreo').addEventListener('click', function() {
        document.getElementById('modalTitulo').textContent = 'Crear Correo';
        document.getElementById('correo').value = '';
        $('#modalCorreo').modal('show');
        document.getElementById('btnGuardarCorreo').onclick = function() {
            const correoNuevo = document.getElementById('correo').value;
            if (correoNuevo) {
                // Realizar la solicitud para agregar un nuevo correo
                fetch('../ajax/solicitud.php?op=addEmail', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            correo: correoNuevo
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            cargarTablaCorreos();
                            $('#modalCorreo').modal('hide');
                        }
                    })
                    .catch(error => console.log(error));
            }
        };
    });

    function editarCorreo(index, correo) {
        document.getElementById('modalTitulo').textContent = 'Editar Correo';
        document.getElementById('correo').value = correo;
        $('#modalCorreo').modal('show');
        document.getElementById('btnGuardarCorreo').onclick = function() {
            const correoEditado = document.getElementById('correo').value;
            if (correoEditado) {
                // Realizar la solicitud para editar el correo
                fetch('../ajax/solicitud.php?op=editEmail', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            index: index,
                            correo: correoEditado
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            cargarTablaCorreos();
                            $('#modalCorreo').modal('hide');
                        }
                    })
                    .catch(error => console.log(error));
            }
        };
    }

    function eliminarCorreo(index) {
        if (confirm("¿Estás seguro de eliminar este correo?")) {
            // Realizar la solicitud para eliminar el correo
            fetch('../ajax/solicitud.php?op=deleteEmail', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        index: index
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cargarTablaCorreos();
                    }
                })
                .catch(error => console.log(error));
        }
    }
    document.getElementById('btnCerrarTabla').addEventListener('click', function() {
        document.getElementById('tablaCorreos').style.display = 'none';
    });
</script>

