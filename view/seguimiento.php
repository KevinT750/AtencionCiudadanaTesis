<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require 'header.php';

if ($_SESSION['Seguimiento'] == 1) {
?>
    <div class="content-wrapper">
        <section class="content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card shadow-lg border-0">
                            <div class="card-header text-center bg-gradient-primary text-white">
                                <h2 class="mb-0"><i class="fa fa-tasks"></i> Mis Solicitudes</h2>
                            </div>
                            <div class="card-body">
                                <h4 class="text-center text-secondary">Seguimiento de Solicitud</h4>
                                <ul id="listaSolicitudes" class="list-group list-group-flush mt-3"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, rgb(68, 150, 237), #0056b3);
        }

        .solicitud-item {
            cursor: pointer;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .solicitud-item:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .seguimiento {
            display: none;
            transition: all 0.3s ease-in-out;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .solicitud-enviada {
            background-color: #e0f7fa;
            border-left: 5px solid #00acc1;
        }

        .solicitud-leida {
            background-color: #fff3e0;
            border-left: 5px solid #ffb74d;
        }

        .solicitud-aprobada {
            background-color: #dcedc8;
            border-left: 5px solid #388e3c;
        }

        .solicitud-rechazada {
            background-color: #ffebee;
            border-left: 5px solid #f44336;
        }

        .icono {
            font-size: 1.5rem;
            margin-right: 10px;
        }

        .card-header h5 {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .btn-ver {
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            padding: 8px 20px;
            transition: background-color 0.3s ease;
        }

        .btn-ver:hover {
            background-color: #0056b3;
        }
    </style>

<?php
} else {
    require 'noacceso.php';
}

require 'footer.php';
?>
<script src="script/seguimiento.js"></script>
<?php
ob_end_flush();
?>