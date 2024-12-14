<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require 'header.php';

if ($_SESSION['Escritorio'] == 1) {
?>
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border" align="center">
                            <h1 class="box-title">
                                <img src="../public/img/logoIST17J.jpg" width="250" height="225" alt="Frontal Image" />
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