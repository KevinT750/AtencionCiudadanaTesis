<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require 'header.php';

if ($_SESSION['Estado'] == 1) {
?>
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header text-center">
                            <h1 class="card-title">Estado de Solicitudes</h1>
                        </div>
                        <div class="card-body">
                            <table id="solicitudesTable" class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Solicitud</th>
                                        <th>Documento</th>
                                        <th>Estado</th>
                                        <th>Cancelar</th> <!-- Nueva columna -->
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
<script src="script/estadoSolicitud.js"></script>
<?php
ob_end_flush();
?>
