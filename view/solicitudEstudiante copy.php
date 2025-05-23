<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require_once 'header.php';
date_default_timezone_set('America/Guayaquil');

if ($_SESSION['Solicitud'] == 1) {

?>

    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border" style="text-align: center;">
                            <h1 class="box-title">Enviar Solicitud</h1>
                        </div>
                        <div class="box-body">
                            <form id="formSolicitud" method="post" enctype="multipart/form-data">

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="titulo">Título de la Solicitud:</label>
                                        <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ingrese un título para su solicitud" required>
                                    </div>
                                </div>

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
                                        <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Ingrese su número de cédula" value="<?php echo $_SESSION['cedula']; ?>" required>
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
                                        <input type="email" class="form-control" id="correo" name="correo" placeholder="Ingrese su correo electrónico" value="<?php echo $_SESSION['correo']; ?>" required>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="Tipo">Tipo:</label>
                                    <select class="form-control" id="Tipo" name="Tipo" required>
                                    
                                    </select>
                                </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="asuntoTexto">Detalles del Asunto:</label>
                                <textarea class="form-control" id="asuntoTexto" name="asuntoTexto" rows="3" placeholder="Ingrese el Asunto" required></textarea>
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
                            <button type="submit" class="btn btn-primary btn-lg mx-2" id="submitBtn">
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
    <script>
        function tipoEstado() {
            const comboBox = document.getElementById('Tipo');
            const textArea = document.getElementById('asuntoTexto');

            if (comboBox && textArea) {
                comboBox.addEventListener("change", function() {
                    const opcion = comboBox.value;
                    switch (opcion) {
                        case "1":
                            textArea.value = "Estimadas autoridades,\n\nPor medio de la presente, solicito de la manera más comedida se me autorice la matrícula en el período académico PAO 2025-I en las asignaturas correspondientes al ……………… nivel de la carrera [nombre de la carrera].\n\nMe comprometo a cumplir con todas las normativas establecidas por la institución y a realizar el respectivo proceso de inscripción dentro de los plazos indicados.\n\nPor la atención prestada, anticipo mis sinceros agradecimientos.";
                            break;

                        case "2":
                            textArea.value = "Estimadas autoridades,\n\nPor medio de la presente, solicito de la manera más comedida se me autorice la segunda matrícula en el período académico PAO 2025-I en las asignaturas: …………………………………………. ……………………………………………………….., pertenecientes al ……………… nivel de la carrera.\n\nPor la atención prestada, anticipo mis sinceros agradecimientos.";
                            break;

                        case "3":
                            textArea.value = "Estimadas autoridades,\n\nMe dirijo a ustedes para solicitar la autorización de mi tercera matrícula en el período académico PAO 2025-I en las asignaturas: …………………………………………. ……………………………………………………….., correspondientes al ……………… nivel de la carrera.\n\nMe comprometo a cumplir con todas las disposiciones reglamentarias y a esforzarme en el desarrollo académico de estas asignaturas.\n\nAgradezco de antemano su atención y consideración.";
                            break;

                        case "4":
                            textArea.value = "Estimadas autoridades,\n\nPor medio de la presente, solicito de manera formal se me autorice el cambio de carrera de [carrera actual] a [nueva carrera] en el período académico PAO 2025-I.\n\nLos motivos de mi solicitud son los siguientes: …………………………………………. ………………………………………………………..\n\nAgradezco su tiempo y consideración para el análisis de mi petición.";
                            break;

                        case "5":
                            textArea.value = "Estimadas autoridades,\n\nPor la presente, solicito se me conceda la homologación de las asignaturas cursadas en [nombre de la institución o carrera anterior] con el objetivo de continuar mis estudios en la carrera de [nombre de la nueva carrera].\n\nAdjunto los documentos necesarios para su respectiva revisión y quedo atento/a a cualquier requerimiento adicional.\n\nAgradezco de antemano su atención y colaboración.";
                            break;

                        default:
                            textArea.value = "";
                            break;
                    }
                });
            }
        }

        // Ejecutar la función al cargar la página
        document.addEventListener("DOMContentLoaded", tipoEstado);

        tipoEstado();
    </script>

<?php
} else {
    require 'noacceso.php';
}

require 'footer.php';
ob_end_flush();
?>