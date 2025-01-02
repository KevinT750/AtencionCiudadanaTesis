<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Solicitudes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
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
                            <table id="solicitudesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Ver Solicitud</th>
                                        <th>Ver Documento</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Las filas se llenarán dinámicamente con AJAX -->
                                </tbody>
                            </table>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

<!-- Modal -->
<div id="miModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Documento</h2>
            <button class="close" id="closeModal">&times;</button>
        </div>
        <iframe id="iframeArchivo" src="" width="100%" height="400px"></iframe>
    </div>
</div>

    <?php
    } else {
        require 'noacceso.php';
    }

    require 'footer.php';
    ob_end_flush();
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
