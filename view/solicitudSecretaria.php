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
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-lg">
                        <div class="card-header bg-primary text-white text-center">
                            <h1 class="card-title mb-0">Solicitudes</h1>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="solicitudesSecret" class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Nombre</th>
                                            <th>Correo Personal</th>
                                            <th>Correo Institucional</th>
                                            <th>Número de Celular</th>
                                            <th>Solicitud</th>
                                            <th>Documento</th>
                                            <th>Estado</th>
                                            <th>Comentario</th> <!-- Nueva columna -->
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

<?php
ob_end_flush();
?>
