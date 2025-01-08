$(document).ready(async function (params) {
    let datosUsu = {};
    let usu_id;

    $('#solicitudesTable').DataTable({
        "ajax": {
            url: "../ajax/usuario.php?op=estadoSolicitud",
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
            { "data": 0 },  // Fecha
            {
                "data": 1,  // ID de solicitud
                "render": function (data, type, row) {
                    return `
                    <button 
                      class="btn btn-info btn-sm ver-solicitud" 
                      data-file-url="https://docs.google.com/document/d/${data}/view">
                      <i class="fa fa-eye"></i> Ver Solicitud
                    </button>`;
                }
            },
            {
                "data": 2,
                "render": function (data, type, row) {
                    return `
                    <button 
                      class="btn btn-success btn-sm ver-otro-dato" 
                      data-file-url="https://drive.google.com/file/d/${data}/view">
                      <i class="fa fa-eye"></i> Ver Documento
                    </button>`;
                }
            },
            {
                "data": 3,  // Estado (Leído o No Leído)
                "render": function (data, type, row) {
                    return `<span class="badge bg-${data === "Leído" ? 'success' : 'warning'}">${data}</span>`;
                }
            },
            {
                "data": null,  // Botón que guarda data de columnas 2 y 3
                "render": function (data, type, row) {
                    // Verificamos si el estado es "Leído"
                    var disableButton = (row[3] === "Leído") ? 'disabled' : ''; 
                    return `
                    <button 
                      class="btn btn-danger btn-sm cancelar-datos" 
                      data-columna-2="${row[1]}" 
                      data-columna-3="${row[2]}" ${disableButton}>
                      <i class="fa fa-trash"></i> Cancelar
                    </button>`;
                }
            }
        ],
        "responsive": true, // Hacer la tabla responsiva
        "paging": true, // Habilitar paginación
        "lengthChange": false, // Deshabilitar la opción de cambiar el número de filas por página
        "info": true, // Mostrar información de la tabla
        "autoWidth": false // Desactivar ajuste automático de ancho de las columnas
    });

    // Evento para abrir el archivo en una nueva ventana
    $('#solicitudesTable').on('click', '.ver-solicitud', function () {
        var fileUrl = $(this).data('file-url'); // Obtener la URL del archivo
        console.log("Ver archivo con URL:", fileUrl);
        // Abrir el archivo en una nueva ventana
        window.open(fileUrl, '_blank');
    });

    // Evento para abrir otro archivo en una nueva ventana
    $('#solicitudesTable').on('click', '.ver-otro-dato', function () {
        var fileUrl = $(this).data('file-url'); // Obtener la URL del archivo
        console.log("Ver archivo con URL:", fileUrl);
        // Abrir el archivo en una nueva ventana
        window.open(fileUrl, '_blank');
    });
});
