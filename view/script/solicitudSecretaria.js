$(document).ready(async function() {
    $('#solicitudesSecret').DataTable({
        "ajax": {
            url: "../ajax/solicitud.php?op=Solicitudes",
            type: "GET",
            data: { tipo: 0 },
            dataSrc: function (json) {
                if (json.aaData) {
                    return json.aaData;
                } else {
                    console.error("Error: No se encontró 'aaData' en la respuesta JSON.", json);
                    return [];
                }
            },
            error: function (xhr, error, thrown) {
                console.error("Error al cargar datos en DataTable:", xhr, error, thrown);
            }
        },
        "columns": [
            { "data": 0 },
            { "data": 1 },
            { "data": 2 },
            { "data": 3 },
            { "data": 4 },
            {
                "data": 5,  // ID de solicitud
                "render": function (data, type, row) {
                    return `
                        <button 
                            class="btn btn-info btn-sm ver-solicitud" 
                            data-file-url="https://docs.google.com/document/d/${data}/view" 
                            title="Ver Solicitud">
                            <i class="fa fa-eye"></i> Ver Solicitud
                        </button>`;
                }
            },
            {
                "data": 6,
                "render": function (data, type, row) {
                    return `
                        <button 
                            class="btn btn-success btn-sm ver-otro-dato" 
                            data-file-url="https://drive.google.com/file/d/${data}/view" 
                            title="Ver Documento">
                            <i class="fa fa-eye"></i> Ver Documento
                        </button>`;
                }
            },
            {
                "data": 7,  // Estado (Leído o No Leído)
                "render": function (data, type, row) {
                    return `<span class="badge bg-${data === "Leído" ? 'success' : 'warning'}" 
                    title="${data === 'Leído' ? 'Leída' : 'No Leída'}">${data}</span>`;
                }
            },
            {
                "data": null,  // Botón que cancela data de columnas 2 y 3
                "render": function (data, type, row) {
                    // Verificamos si el estado es "Leído"
                    var disableButton = (row[3] === "Leído") ? 'disabled' : ''; 
                    return `
                        <button 
                            class="btn btn-danger btn-sm cancelar-datos" 
                            data-columna-2="${row[1]}" 
                            data-columna-3="${row[2]}" ${disableButton} 
                            title="${disableButton ? 'No se puede cancelar (ya leído)' : 'Dejar comentario'}">
                            <i class="fa fa-comments"></i> Dejar Comentario
                        </button>`;
                }
            }
            
        ],
        "dom": '<"top"f>rt<"bottom"ilp><"clear">',
        "language": {
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros en total)",
        },
        "pagingType": "simple_numbers",
        "ordering": true,
        "searching": true,
        "responsive": true,
        "autoWidth": false
    });

    // Evento para mostrar un mensaje emergente cuando se pase el mouse por encima de un botón
    $(document).on('mouseenter', '.btn', function() {
        $(this).tooltip('show');
    });

    $(document).on('mouseleave', '.btn', function() {
        $(this).tooltip('hide');
    });

    $('#solicitudesSecret').on('click', '.ver-solicitud', function () {
        var fileUrl = $(this).data('file-url'); // Obtener la URL del archivo
        console.log("Ver archivo con URL:", fileUrl);
        // Abrir el archivo en una nueva ventana
        window.open(fileUrl, '_blank');
    });

    // Evento para abrir otro archivo en una nueva ventana
    $('#solicitudesSecret').on('click', '.ver-otro-dato', function () {
        var fileUrl = $(this).data('file-url'); // Obtener la URL del archivo
        console.log("Ver archivo con URL:", fileUrl);
        // Abrir el archivo en una nueva ventana
        window.open(fileUrl, '_blank');
    });
});
