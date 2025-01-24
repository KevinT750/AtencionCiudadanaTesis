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
                            data-file-url="https://drive.google.com/file/d/${data}/preview" 
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
                            data-file-url="https://drive.google.com/file/d/${data}/preview" 
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
                            data-columna-2="${row[5]}" 
                            data-columna-3="${row[6 ]}" ${disableButton} 
                            title="${disableButton ? 'No se puede cancelar (ya leído)' : 'Dejar comentario'}">
                            <i class="fa fa-comments"></i> Dejar Comentario
                        </button>`;
                        // columna2 Solicitud y 3 documentos
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
        const id = $(this).data('file-url');
        modalSol(id);
    });
    
    // Evento para ver otro dato
    $('#solicitudesSecret').on('click', '.ver-otro-dato', function () {
        const fileUrl = $(this).data('file-url');
        console.log("Ver archivo con URL:", fileUrl);
        const id = $(this).data('file-url');
        modalSol(id);
    });

    // Evento para cancelar datos
    $('#solicitudesSecret').on('click', '.cancelar-datos', function () {
        const columna2 = $(this).data('columna-2');
        const columna3 = $(this).data('columna-3');
    
        $.ajax({
            url: "../ajax/solicitud.php?op=modalSecretaria",
            type: "POST", // Cambiado a POST
            data: {
                columna2: columna2,
                columna3: columna3
            },
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
    
    function modalSol(idDrive) {
        const data = {
            id: idDrive,
        };
    
        $.ajax({
            url: "../ajax/solicitud.php?op=modalSol",  // Ruta al archivo PHP
            type: "POST",
            dataType: "json",  // Asegúrate de que la respuesta se maneje como JSON
            data: data,
            success: function(response) {
                // Verifica si la respuesta contiene el contenido necesario
                if (response && response.modalContent) {
                    // Inserta el contenido del modal en el body
                    const modalContainer = $('<div>').html(response.modalContent);
                    $('body').append(modalContainer);
    
                    // Asegúrate de que el modal y el overlay tengan la clase show para hacerlos visibles
                    $('#documentModal').addClass('show');
                    $('#overlay').addClass('show');
    
                    // Opcional: Añadir un listener para cerrar el modal cuando el overlay sea clickeado
                    $('#overlay').on('click', function() {
                        $('#documentModal').remove();  // Eliminar modal
                        $('#overlay').remove();        // Eliminar overlay
                    });
    
                    // Añadir funcionalidad de cerrar el modal con el botón de cerrar
                    $('.modal-close').on('click', function() {
                        $('#documentModal').remove();  // Eliminar modal
                        $('#overlay').remove();        // Eliminar overlay
                    });
                } else {
                    Swal.fire("Error", "La respuesta del servidor no contiene el contenido esperado.", "error");
                }
            },
            error: function() {
                Swal.fire("Error", "No se pudo cargar el modal. Intenta de nuevo.", "error");
            }
        });
    }
    
    

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
    
                inicializarAgregarCorreo();
            },
            error: function () {
                Swal.fire("Error", "No se pudo cargar el modal. Intenta de nuevo.", "error");
            }
        });
    }
    
    function inicializarAgregarCorreo() {
        const agregarCorreoBtn = document.querySelector("[name='agregarCorreo']"); // Seleccionar el botón "+"
        const contenedorCorreos = document.getElementById("contenedorCorreos");
        let correosSeleccionados = [];
        
        // Función para actualizar los correos seleccionados
        function actualizarCorreosSeleccionados() {
            const todosSelects = document.querySelectorAll('select[name="correoSeleccionado"]');
            correosSeleccionados = [];
            
            todosSelects.forEach(select => {
                const correoSeleccionado = select.value;
                if (correoSeleccionado && correoSeleccionado !== "") {
                    correosSeleccionados.push(correoSeleccionado);
                }
            });
        }
        
        // Función para evitar agregar más correos si ya están todos seleccionados
        function verificarCorreosDisponibles(correosDisponibles) {
            // Si todos los correos han sido seleccionados, deshabilitamos el botón "+"
            const correosRestantes = correosDisponibles.filter(correo => !correosSeleccionados.includes(correo));
            if (correosRestantes.length === 0) {
                agregarCorreoBtn.disabled = true;  // Deshabilitar el botón "+"
                Swal.fire("¡Todos los correos ya han sido seleccionados!", "", "info");
            } else {
                agregarCorreoBtn.disabled = false;  // Habilitar el botón "+"
            }
        }
    
        // Evitar el envío del formulario cuando se hace clic en el botón "+"
        agregarCorreoBtn.addEventListener("click", function (event) {
            event.preventDefault(); // Prevenir la recarga de la página al hacer clic en "+"
            
            // Cargar los correos desde el archivo JSON
            fetch('../Mailer/emails.json')
                .then(response => response.json())
                .then(correos => {
                    // Actualizar la lista de correos seleccionados
                    actualizarCorreosSeleccionados();
    
                    // Verificar si aún hay correos disponibles
                    verificarCorreosDisponibles(correos);
    
                    // Si todos los correos ya fueron seleccionados, no permitir agregar más
                    if (agregarCorreoBtn.disabled) return;
    
                    // Crear un nuevo contenedor para el combo (select)
                    const nuevoCorreoDiv = document.createElement("div");
                    nuevoCorreoDiv.className = "d-flex align-items-center mb-2";
                    
                    // Crear un nuevo ComboBox (select)
                    const nuevoCorreoSelect = document.createElement("select");
                    nuevoCorreoSelect.className = "form-control me-2";
                    nuevoCorreoSelect.name = "correoSeleccionado"; // Agregar un nombre para el campo select
                    
                    // Añadir las opciones al ComboBox
                    correos.forEach(function(correo) {
                        const nuevaOpcion = document.createElement("option");
                        nuevaOpcion.value = correo;
                        nuevaOpcion.textContent = correo;
                        if (correosSeleccionados.includes(correo)) {
                            nuevaOpcion.disabled = true; // Deshabilitar los correos ya seleccionados
                        }
                        nuevoCorreoSelect.appendChild(nuevaOpcion);
                    });
    
                    // Crear un botón para eliminar este ComboBox
                    const eliminarCorreoBtn = document.createElement("button");
                    eliminarCorreoBtn.type = "button";
                    eliminarCorreoBtn.className = "btn btn-danger";
                    eliminarCorreoBtn.textContent = "-";
                    
                    // Eliminar el ComboBox al hacer clic en el botón "-"
                    eliminarCorreoBtn.addEventListener("click", function () {
                        contenedorCorreos.removeChild(nuevoCorreoDiv);
                        actualizarCorreosSeleccionados(); // Actualizar la lista de correos seleccionados
                        verificarCorreosDisponibles(correos); // Verificar nuevamente si se pueden agregar más correos
                    });
                    
                    // Añadir el select y el botón al contenedor
                    nuevoCorreoDiv.appendChild(nuevoCorreoSelect);
                    nuevoCorreoDiv.appendChild(eliminarCorreoBtn);
                    
                    // Agregar el nuevo contenedor al contenedor principal
                    contenedorCorreos.appendChild(nuevoCorreoDiv);
                    
                    // Actualizar la lista de correos seleccionados para que no se repitan
                    actualizarCorreosSeleccionados();
                    verificarCorreosDisponibles(correos); // Verificar si se pueden agregar más correos
                })
                .catch(error => {
                    console.error("Error al cargar el archivo JSON:", error);
                    Swal.fire("Error", "No se pudo cargar los correos. Intenta de nuevo.", "error");
                });
        });
    }
    
});
