<?php

use Google\Service\Batch\Script;

ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require 'header.php';

if ($_SESSION['Reporte'] == 1) {
?>
    <link rel="stylesheet" href="../public/css/estadistica.css">

    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="container">
                        <div class="container-form">
                            <form class="coding" id="very-form">
                                <span class="titulo">Estadísticas de Atención Ciudadana</span>
                                <div class="container-select">
                                    <div class="select-group">
                                        <label for="anio">Seleccione el año:</label>
                                        <select id="anio">
                                            <option value="">Seleccione el Año</option>
                                            <?php
                                            $anio = 2024;
                                            $anio_actual = date('Y');
                                            for ($i = $anio; $i <= $anio_actual; $i++) {
                                                echo "<option value='$i'>$i</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="select-group">
                                        <label for="mes">Seleccione el mes:</label>
                                        <select id="mes" disabled onchange="activarSelect()">
                                            <option value="">Seleccione el Mes</option>
                                            <?php
                                            $meses = [
                                                '01' => 'Enero',
                                                '02' => 'Febrero',
                                                '03' => 'Marzo',
                                                '04' => 'Abril',
                                                '05' => 'Mayo',
                                                '06' => 'Junio',
                                                '07' => 'Julio',
                                                '08' => 'Agosto',
                                                '09' => 'Septiembre',
                                                '10' => 'Octubre',
                                                '11' => 'Noviembre',
                                                '12' => 'Diciembre'
                                            ];
                                            foreach ($meses as $key => $value) {
                                                echo "<option value='$key'>$value</option>";
                                            }
                                            ?>
                                        </select>

                                    </div>
                                    <div class="select-group">
                                        <label for="asunto">Seleccione el asunto:</label>
                                        <select id="asunto" disabled></select>
                                    </div>
                                </div>
                                <button type="submit">Buscar</button>
                            </form>
                        </div>
                    </div>

                    <div class="container1">
                        <div class="container-pieChart">
                            <canvas id="pieChart" width="400" height="300"></canvas>

                        </div>
                        <div class="container-lineChart">
                            <canvas id="lineChart" width="800" height="400"></canvas>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="../view/script/estadistica.js"></script>