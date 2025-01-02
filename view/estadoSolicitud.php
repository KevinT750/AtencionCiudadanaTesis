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
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Solicitud</th>
                                        <th>Documento</th>
                                        <th>Estado</th>
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

    <!-- Modal para ver documentos -->
    <div class="modal fade" id="modalDocument" tabindex="-1" role="dialog" aria-labelledby="modalDocumentLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDocumentLabel">Ver Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="documentViewer" src="" width="100%" height="400px"></iframe>
                </div>
            </div>
        </div>
    </div>

<?php
} else {
    require 'noacceso.php';
}

require 'footer.php';
ob_end_flush();
?>
