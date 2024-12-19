<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require 'header.php';

if ($_SESSION['Solicitud'] == 1) {

?>

    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border" align="center">
                            <h1 class="box-title">Enviar Solicitud</h1>
                        </div>
                        <div class="box-body">
                            <form id="formSolicitud" method="post" enctype="multipart/form-data" action="descargar_con_datos.php">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="fecha">Fecha:</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" min="2024-01-01" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="nombre">Nombres Completos:</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese sus nombres completos" value="<?php echo $_SESSION['usu_nombre']; ?>" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="cedula">Número de Cédula:</label>
                                        <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Ingrese su número de cédula" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="carrera">Carrera:</label>
                                        <select class="form-control" id="carrera" name="carrera" required>
                                            <option value="">Seleccione su carrera</option>
                                            <option value="Desarrollo de Software">Desarrollo de Software</option>
                                            <option value="Redes y Telecomunicaciones">Redes y Telecomunicaciones</option>
                                            <option value="Electrónica">Electrónica</option>
                                            <option value="Automatización e Instrumentación">Automatización e Instrumentación</option>
                                            <option value="Electricidad">Electricidad</option>
                                            <option value="Mecánica Industrial">Mecánica Industrial</option>
                                            <option value="Mecánica Automotriz">Mecánica Automotriz</option>
                                            <option value="Entrenamiento Deportivo">Entrenamiento Deportivo</option>
                                            <option value="Procesamiento de Alimentos">Procesamiento de Alimentos</option>
                                            <option value="Química">Química</option>
                                            <option value="Biotecnología">Biotecnología</option>
                                            <option value="Control de Incendios y Operaciones de Rescate">Control de Incendios y Operaciones de Rescate</option>
                                            <option value="Idiomas (Certificación B1)">Idiomas (Certificación B1)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="telefono">Teléfono Domicilio:</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Teléfono domicilio (Opcional)">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="celular">Celular:</label>
                                        <input type="text" class="form-control" id="celular" name="celular" placeholder="Número de celular" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="correo">Correo Electrónico:</label>
                                        <input type="email" class="form-control" id="correo" name="correo" placeholder="Ingrese su correo electrónico" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="asuntoTexto">Detalles del Asunto:</label>
                                        <textarea class="form-control" id="asuntoTexto" name="asuntoTexto" rows="3"   placeholder="Ingrese el Asunto" require></textarea>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="archivo">Adjuntar Cédula (PDF):</label>
                                        <input type="file" class="form-control-file" id="archivo" name="archivo" accept="application/pdf">
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <button type="button" class="btn btn-success btn-lg mx-2" onclick="window.location.href='descargar.php'">
                                        <i class="fa fa-download"></i> Descargar Solicitud
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-lg mx-2">
                                        <i class="fa fa-paper-plane"></i> Enviar Solicitud
                                    </button>
                                    <button type="reset" class="btn btn-danger btn-lg mx-2">
                                        <i class="fa fa-trash"></i> Limpiar
                                    </button>
                                </div>
                            </form>
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
