<?php
if (strlen(session_id()) < 1) 
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Acceso Denegado</title>
</head>
<body>
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h1 class="box-title">Acceso Denegado</h1>
                        </div>
                        <div class="box-body">
                            <div class="alert alert-danger">
                                <h4><i class="icon fa fa-ban"></i> Acceso Denegado!</h4>
                                No tiene permisos para acceder a esta sección.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>