$(document).ready(function () {
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
                "data": null,  // Botón que cancela datos de columnas 2 y 3
                "render": function (data, type, row) {
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
    $(document).on('mouseenter', '.btn', function () {
        $(this).tooltip('show');
    });

    $(document).on('mouseleave', '.btn', function () {
        $(this).tooltip('hide');
    });

    // Evento para ver solicitud
    $('#solicitudesSecret').on('click', '.ver-solicitud', function () {
        const fileUrl = $(this).data('file-url');
        console.log("Ver archivo con URL:", fileUrl);
        window.open(fileUrl, '_blank');
    });

    // Evento para ver otro dato
    $('#solicitudesSecret').on('click', '.ver-otro-dato', function () {
        const fileUrl = $(this).data('file-url');
        console.log("Ver archivo con URL:", fileUrl);
        window.open(fileUrl, '_blank');
    });

    // Evento para cancelar datos
    $('#solicitudesSecret').on('click', '.cancelar-datos', function () {
        const columna2 = $(this).data('columna-2');
        const columna3 = $(this).data('columna-3');

        $.ajax({
            url: "../ajax/solicitud.php?op=modalSecretaria",
            type: "GET",
            success: function (response) {
                $('body').append(response); // Agregar el modal dinámicamente al DOM
                $('#modalSubir').css('display', 'block'); // Mostrar el modal sin fondo oscuro
    
                const btnAprobar = $("#btnAprobar");
                const btnRechazar = $("#btnRechazar");
                const mensajeArea = $("#mensajeArea");
                const btnEnviar = $("#btnEnviar");
    
                btnAprobar.on("click", function () {
                    Swal.fire({
                        title: "Solicitud Aprobada",
                        text: "¿Estás seguro de proceder?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Sí, proceder",
                        cancelButtonText: "Cancelar",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            mostrarModalAprobar();
                            cerrarModal();
                        }
                    });
                });
    
                btnRechazar.on("click", function () {
                    Swal.fire({
                        title: "Solicitud Rechazada",
                        text: "¿Estás seguro de proceder?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Sí, proceder",
                        cancelButtonText: "Cancelar",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            mensajeArea.show();
                            $("#mensaje").focus();
                        }
                    });
                });
    
                btnEnviar.on("click", function () {
                    const mensaje = $("#mensaje").val().trim();
                    if (mensaje !== "") {
                        Swal.fire({
                            icon: "success",
                            title: "Mensaje enviado",
                            text: "El mensaje ha sido enviado exitosamente.",
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Por favor, escribe un mensaje antes de enviar.",
                        });
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error("Hubo un error al cargar el modal:", error);
            }
        });
    });

    function cerrarModal() {
        $('#modalSubir').remove(); // Eliminar el modal del DOM
        $('#overlay').hide();
    }

    function mostrarModalAprobar() {
        $.ajax({
            url: "../ajax/solicitud.php?op=modalAprobar",
            type: "GET",
            success: function (response) {
                const modalContainer = $('<div>').html(response);
                $('body').append(modalContainer);
                $('#modalEnviarSolicitud').show();
                $('#overlay').show();
    
                // Llama al método para inicializar la funcionalidad de agregar correos
                inicializarAgregarCorreo();
            },
            error: function () {
                Swal.fire("Error", "No se pudo cargar el modal. Intenta de nuevo.", "error");
            }
        });
    }
    
    // Método para inicializar la funcionalidad de agregar correos
    function inicializarAgregarCorreo() {
        const agregarCorreoBtn = document.getElementById("agregarCorreo");
        const contenedorCorreos = document.getElementById("contenedorCorreos");
    
        if (agregarCorreoBtn && contenedorCorreos) {
            agregarCorreoBtn.addEventListener("click", function () {
                // Crear un nuevo contenedor para el correo
                const nuevoCorreoDiv = document.createElement("div");
                nuevoCorreoDiv.className = "d-flex align-items-center mb-2";
    
                // Crear un nuevo campo de correo
                const nuevoCorreoInput = document.createElement("input");
                nuevoCorreoInput.type = "email";
                nuevoCorreoInput.className = "form-control me-2";
                nuevoCorreoInput.placeholder = "Buscar correo...";
                nuevoCorreoInput.required = true;
    
                // Crear un botón para eliminar este campo
                const eliminarCorreoBtn = document.createElement("button");
                eliminarCorreoBtn.type = "button";
                eliminarCorreoBtn.className = "btn btn-danger";
                eliminarCorreoBtn.textContent = "-";
    
                // Eliminar el campo al hacer clic en el botón "-"
                eliminarCorreoBtn.addEventListener("click", function () {
                    contenedorCorreos.removeChild(nuevoCorreoDiv);
                });
    
                // Añadir el input y el botón al contenedor
                nuevoCorreoDiv.appendChild(nuevoCorreoInput);
                nuevoCorreoDiv.appendChild(eliminarCorreoBtn);
    
                // Agregar el nuevo contenedor al contenedor principal
                contenedorCorreos.appendChild(nuevoCorreoDiv);
            });
        }
    }
    
});
