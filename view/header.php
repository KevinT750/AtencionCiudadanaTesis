<?php
if (strlen(session_id()) < 1)
    session_start();

$permisos = [
    'Escritorio',
    'Descargar',
    'Solicitud',
    'Atencion',
    'Estado',
    'Seguimiento',
    'Aprobadas',
    'Ver_Solicitudes',
    'Subir_Solicitud',
    'Gestion',
    'Reporte',
    'Estudiante',
    'Secretaria'
];

foreach ($permisos as $permiso) {
    if (!isset($_SESSION[$permiso])) {
        $_SESSION[$permiso] = 1;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ATENCIÓN CIUDADANA 17J</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="../public/datatables/responsive.dataTables.min.css">
    <link rel="stylesheet" href="../public/css/bootstrap.min.css">
    <link rel="stylesheet" href="../public/css/font-awesome.min.css">
    <link rel="stylesheet" href="../public/css/AdminLTE.min.css">
    <link rel="stylesheet" href="../public/css/_all-skins.min.css">
    <link rel="stylesheet" href="../public/datatables/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../public/datatables/buttons.dataTables.min.css">
    <link rel="stylesheet" href="../public/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="../public/css/daterangepicker.css">
    <link href="../public/datatables/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="../public/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/model.css">



</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            <a href="escritorio.php" class="logo">
                <span class="logo-mini">
                    <font size="4">=></font>
                </span>
                <span class="logo-lg">
                    <font size="4">MENÚ</font>
                </span>
            </a>
            <nav class="navbar navbar-static-top">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span>
                        <font size="4" style="color:#FFF">ATENCIÓN CIUDADANA 17J</font>
                    </span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- NOTIFICACIONES (Solo para estudiantes) -->
                        <?php if (strtoupper($_SESSION['Rol']) === 'ESTUDIANTE' && $_SESSION['Estudiante'] == 1): ?>
                            <li class="dropdown notifications-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-bell-o"></i>
                                    <span id="notificacion-count" class="label label-warning">0</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">Tienes <span id="notificacion-text">0</span> notificaciones</li>
                                    <li>
                                        <ul class="menu" id="notificacion-list" style="max-height: 300px; overflow-y: auto;">
                                            <!-- Aquí se insertan dinámicamente las notificaciones -->
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="seguimiento.php">Ver todas</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <!-- MENÚ DEL USUARIO -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="hidden-xs"><?php echo $_SESSION['usu_nombre']; ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="user-header">
                                    <p>
                                        <?php echo $_SESSION['usu_nombre']; ?>
                                        <small>IST17J - <?php echo $_SESSION['Rol']; ?></small>
                                    </p>
                                </li>
                                <li class="user-footer">
                                    <div class="pull-right">
                                        <a href="../ajax/usuario.php?op=salir" class="btn btn-default btn-flat">Salir</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="main-sidebar">
            <section class="sidebar">
                <ul class="sidebar-menu" data-widget="tree">
                    <br>

                    <!-- MENÚ PARA ESTUDIANTE -->
                    <?php if (strtoupper($_SESSION['Rol']) === 'ESTUDIANTE' && $_SESSION['Estudiante'] == 1): ?>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i> <span>Solicitud</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">

                                <li><a href="solicitudEstudiante.php">Solicitud de Estudiante</a></li>
                                <li><a href="atencionCiudadana.php">Atención Ciudadana</a></li>
                            </ul>
                        </li>

                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i> <span>Mis Solicitudes</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">

                                <li><a href="solicitudEstado.php">Solicitudes</a></li>
                                <li><a href="seguimiento.php">Seguimiento de Solicitudes</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- MENÚ PARA SECRETARIA -->
                    <?php if (strtoupper($_SESSION['Rol']) === 'SECRETARIA' && $_SESSION['Secretaria'] == 1): ?>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder-open"></i> <span>Solicitudes</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="solicitudSecretaria.php">Ver Solicitudes</a></li>
                                <li><a href="SubirSolicitud.php">Subir Solicitudes</a></li>
                                <li><a href="gestionSolicitudes.php">Gestión de Solicitudes</a></li>
                            </ul>
                        </li>

                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i> <span>Reporte</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="generarReporte.php">Generar Reporte</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                </ul>
            </section>
        </aside>
</body>

</html>