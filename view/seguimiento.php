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
                        <div class="box-header with-border text-center">
                            <h1 class="box-title">
                                <img src="../public/img/logoIST17J.jpg" width="250" height="225" alt="Frontal Image" />
                            </h1>
                        </div>
                        <div class="box-body">
                            <h3 class="text-center">Seguimiento de Solicitud</h3>
                            <canvas id="seguimientoChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Agregar Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            let sol_id = 101; // Cambiar por el ID de solicitud dinámico

            $.getJSON(`../ajax/seguimiento.php?sol_id=${sol_id}`, function(data) {
                let fechas = [];
                let acciones = [];
                let estados = [];

                data.forEach(evento => {
                    fechas.push(evento.seg_fecha);
                    acciones.push(evento.seg_accion);
                    estados.push(evento.seg_visto == 1 ? 'Visto' : 'No visto');
                });

                let ctx = document.getElementById('seguimientoChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: fechas,
                        datasets: [{
                            label: 'Seguimiento de Solicitud',
                            data: acciones.map((_, index) => index + 1), // Solo para la línea de tiempo
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return acciones[tooltipItem.dataIndex] + " (" + estados[tooltipItem.dataIndex] + ")";
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Fecha del Evento'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Progreso'
                                },
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });
        });
    </script>
<?php
} else {
    require 'noacceso.php';
}

require 'footer.php';
ob_end_flush();
?>
