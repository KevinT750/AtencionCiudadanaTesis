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
    <style>
        /* Estilos para los botones */
        .btn-outline-primary {
            font-size: 14px;
            padding: 8px 16px;
            text-transform: uppercase;
            font-weight: bold;
            border-radius: 25px;
            transition: all 0.3s ease;
            border-color: #007bff;
            color: #007bff;
        }

        .btn-outline-primary:hover {
            background-color: #007bff;
            color: #fff;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
            transform: translateY(-2px);
        }

        .btn-outline-success {
            font-size: 14px;
            padding: 8px 16px;
            text-transform: uppercase;
            font-weight: bold;
            border-radius: 25px;
            transition: all 0.3s ease;
            border-color: #28a745;
            color: #28a745;
        }

        .btn-outline-success:hover {
            background-color: #28a745;
            color: #fff;
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }

        .btn-outline-info {
            font-size: 14px;
            padding: 8px 16px;
            text-transform: uppercase;
            font-weight: bold;
            border-radius: 25px;
            transition: all 0.3s ease;
            border-color: #17a2b8;
            color: #17a2b8;
        }

        .btn-outline-info:hover {
            background-color: #17a2b8;
            color: #fff;
            box-shadow: 0 4px 8px rgba(23, 162, 184, 0.3);
            transform: translateY(-2px);
        }

        .btn-outline-danger {
            font-size: 14px;
            padding: 8px 16px;
            text-transform: uppercase;
            font-weight: bold;
            border-radius: 25px;
            transition: all 0.3s ease;
            border-color: #dc3545;
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: #fff;
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
            transform: translateY(-2px);
        }

        .btn-outline-secondary {
            font-size: 14px;
            padding: 8px 16px;
            text-transform: uppercase;
            font-weight: bold;
            border-radius: 25px;
            transition: all 0.3s ease;
            border-color: #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: #fff;
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
            transform: translateY(-2px);
        }

        /* Efecto de sombras para los botones */
        .shadow-sm {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        }

        .m-1 {
            margin: 5px;
        }

        /* Íconos dentro de los botones */
        .btn i {
            margin-right: 5px;
        }

        /* Estilos para el botón con los iconos */
        .btn i {
            margin-right: 10px;
            /* Espacio entre el ícono y el texto */
        }
    </style>
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-lg">
                        <div class="card-header bg-primary text-white text-center">
                            <h1 class="card-title mb-0">Estado de Solicitudes</h1>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="estadoFiltro" class="form-label">Filtrar por Estado</label>
                                <div id="estadoFiltro"></div> <!-- Aquí se genera el filtro -->
                            </div>
                            <div class="table-responsive">
                                <table id="solicitudesTable" class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Solicitud</th>
                                            <th>Documento</th>
                                            <th>Estado</th>
                                            <th>Cancelar</th> <!-- Nueva columna -->
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
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
<script src="script/estadoSolicitud.js"></script>
<?php
ob_end_flush();
?>