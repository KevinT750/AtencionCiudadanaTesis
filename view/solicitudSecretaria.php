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
    <style>
        .list-group {
            position: absolute;
            z-index: 1000;
            width: 100%;
            background: white;
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: auto;
            padding: 0;
            margin: 0;
            list-style-type: none;
        }

        .list-group li {
            padding: 8px 12px;
            cursor: pointer;
        }

        .list-group li:hover {
            background-color: #f0f0f0;
        }
    </style>

    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- Botón para mostrar el formulario -->
                    <?php if ($_SESSION['Subir_Solicitud'] == 1) { ?>
                        <button id="btnMostrarFormulario" class="btn btn-success btn-lg mb-4">
                            <i class="fa fa-plus-circle"></i> ¿Quieres subir una solicitud?
                        </button>

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
                    <?php } ?>

                    <!-- Tabla de solicitudes -->
                    <div class="card shadow-lg">
                        <div class="card-header bg-primary text-white text-center">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-file-alt me-2"></i>Gestión de Solicitudes
                            </h3>
                        </div>
                        <div class="card-body">
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
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
    // Mostrar el formulario al hacer clic en el botón
    document.getElementById('btnMostrarFormulario').addEventListener('click', function() {
        document.getElementById('formularioSolicitud').style.display = 'block';
    });

    // Cancelar y ocultar el formulario al hacer clic en el botón de cancelar
    document.getElementById('btnCancelar').addEventListener('click', function() {
        document.getElementById('formularioSolicitud').style.display = 'none';
    });
</script>
<?php
ob_end_flush();
?>