<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b>
    </div>
    <strong>Copyright &copy; 2021 <a target="_blank" href="https://web.ist17dejulio.edu.ec/">IST17J</a>.</strong> (jlnq) Todo los derechos reservados.
</footer>

<script src="../public/js/jquery.min.js"></script>
<script src="../public/datatables/jquery.dataTables.min.js"></script>
<script src="../public/datatables/dataTables.buttons.min.js"></script>
<script src="../public/datatables/buttons.colVis.min.js"></script>
<script src="../public/datatables/buttons.html5.min.js"></script>
<script src="../public/datatables/jszip.min.js"></script>
<script src="../public/datatables/pdfmake.min.js"></script>
<script src="../public/datatables/vfs_fonts.js"></script>
<script src="../public/js/moment.min.js"></script>
<script src="../public/js/bootstrap.min.js"></script>
<script src="../public/js/adminlte.min.js"></script>
<script src="../public/js/daterangepicker.js"></script>
<script src="../public/js/bootbox.min.js"></script>
<script src="../public/js/bootstrap-select.min.js"></script>
<script>
    const fechaActual = new Date();
const dia = fechaActual.getDate();
const mes = fechaActual.getMonth() + 1; // Sumamos 1 para obtener el mes en formato humano
const anio = fechaActual.getFullYear();

// Formateamos la fecha como "YYYY-MM-DD" (formato requerido por el input de tipo date)
const fechaFormateada = `${anio}-${mes.toString().padStart(2, '0')}-${dia.toString().padStart(2, '0')}`;

console.log(`Fecha formateada: ${fechaFormateada}`);

</script>

</body>
</html>
