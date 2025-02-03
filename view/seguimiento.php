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
                            <canvas id="seguimientoChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let sol_id = 101; // Cambia esto dinámicamente según la solicitud

            $.getJSON(`../ajax/seguimiento.php?sol_id=${sol_id}`, function(data) {
                let labels = [];
                let fechas = [];
                let acciones = [];

                data.forEach(evento => {
                    labels.push(evento.seg_accion);
                    fechas.push(evento.seg_fecha);
                    acciones.push(evento.seg_visto == 1 ? 1 : 0); // Visto (1) / No visto (0)
                });

                const ctx = document.getElementById('seguimientoChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: fechas,
                        datasets: [{
                            label: 'Seguimiento de la Solicitud',
                            data: acciones,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderWidth: 2,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return labels[tooltipItem.dataIndex];
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value === 1 ? 'Visto' : 'No visto';
                                    }
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
