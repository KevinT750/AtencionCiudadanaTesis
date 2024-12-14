<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require 'header.php';

if ($_SESSION['Descargar'] == 1) {
?>
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border" align="center">
                            <h1 class="box-title">
                                <h3 center>Descargar Solicitud de Estudiante</h3>
                            <button class="btn btn-primary" onclick="window.location.href='descargar.php'">
                                Descargar
                            </button>
                            <button class="btn btn-success" onclick="window.location.href='descargar_con_datos.php'">
                                Descargar con Datos
                            </button>
                            </h1>
                            <div class="box-tools pull-right"></div>
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
ob_end_flush();
?>