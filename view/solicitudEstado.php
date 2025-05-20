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
        .estado-noleido {
            background-color: #d0e4f7 !important;
            /* azul claro */
        }

        .estado-leido {
            background-color: rgb(237, 237, 139) !important;
            /* gris claro */
        }

        .estado-aprobado {
            background-color: #d4edda !important;
            /* verde claro */
        }

        .estado-rechazado {
            background-color: #f8d7da !important;
            /* rojo claro */
        }


        #btn-no-leido {
            color: #0d6efd;
            /* azul bootstrap */
        }

        #btn-no-leido:hover {
            background-color: rgba(13, 110, 253, 0.15);
        }

        #btn-leido {
            color: #60d6ee;
            /* verde bootstrap */
        }

        #btn-leido:hover {
            background-color: rgba(25, 135, 84, 0.15);
        }

        #btn-aceptado {
            color: #198754;
            /* cyan bootstrap */
        }

        #btn-aceptado:hover {
            background-color: rgba(13, 202, 240, 0.15);
        }

        #btn-rechazado {
            color: #dc3545;
            /* rojo bootstrap */
        }

        #btn-rechazado:hover {
            background-color: rgba(220, 53, 69, 0.15);
        }

        #btn-todos {
            color: #6c757d;
            /* gris bootstrap */
        }

        #btn-todos:hover {
            background-color: rgba(108, 117, 125, 0.15);
        }

        #btn-no-leido,
        #btn-leido,
        #btn-aceptado,
        #btn-rechazado,
        #btn-todos {
            border: none !important;
            background-color: transparent !important;
            box-shadow: none !important;
            padding: 0.375rem 1rem;
            border-radius: 50rem !important;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }


        #btn-no-leido:hover {
            background-color: rgba(0, 123, 255, 0.1);
            /* Azul claro al hover */
        }

        #btn-leido:hover {
            background-color: rgba(40, 167, 69, 0.1);
            /* Verde claro al hover */
        }

        #btn-aceptado:hover {
            background-color: rgba(23, 162, 184, 0.1);
            /* Azul info claro */
        }

        #btn-rechazado:hover {
            background-color: rgba(220, 53, 69, 0.1);
            /* Rojo claro */
        }

        #btn-todos:hover {
            background-color: rgba(108, 117, 125, 0.1);
            /* Gris claro */
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