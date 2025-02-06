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
                            data-columna-2="${row[5]}" 
                            data-columna-3="${row[6]}"
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
                "data": 7,  // Estado (Enviado, Leído, Aceptado, Rechazado, No Leído)
                "render": function (data, type, row) {
                    // Determinar la clase de fondo y el título según el estado
                    let badgeClass, titleText;
            
                    switch (data) {
                        case "Enviado":
                            badgeClass = "primary";
                            titleText = "Solicitud Enviada";
                            break;
                        case "Leído":
                            badgeClass = "success";
                            titleText = "Solicitud Leída";
                            break;
                        case "Aceptado":
                            badgeClass = "info";
                            titleText = "Solicitud Aceptada";
                            break;
                        case "Rechazado":
                            badgeClass = "danger";
                            titleText = "Solicitud Rechazada";
                            break;
                        case "No Leído":
                            badgeClass = "warning";
                            titleText = "Solicitud No Leída";
                            break;
                        default:
                            badgeClass = "secondary";
                            titleText = "Estado Desconocido";
                            break;
                    }
            
                    // Retornar el span con clase y título dinámico
                    return `<span class="badge bg-${badgeClass}" title="${titleText}">${data}</span>`;
                }
            },
            
            {
                "data": null,  // Botón que cancela datos de columnas 2 y 3
                "render": function (data, type, row) {
                    // Verificar si el estado del botón requiere estar deshabilitado
                    const estado = row[7]; // Asegúrate de que el estado esté en la columna 7
                    const disableButton = (estado !== "No Leído" && estado !== "Leído") ? 'disabled' : '';
                    const titleText = disableButton 
                        ? `No puede Dejar un comentario (${estado.toLowerCase()})` 
                        : 'Dejar comentario';
            
                    // Retornar el botón con la condición aplicada
                    return `
                        <button 
                            class="btn btn-danger btn-sm cancelar-datos" 
                            data-id="${row[8]}"
                            data-columna-2="${row[5]}" 
                            data-columna-3="${row[6]}" ${disableButton} 
                            title="${titleText}">
                            <i class="fa fa-comments"></i> Dejar Comentario
                        </button>`;
                }
            }
            
        ],
        "dom": '<"top"f>rt<"bottom"ilp><"clear">', // Elimina los botones PDF, Print, etc.
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
        "autoWidth": false,
        "buttons": [
            // Agregamos solo el botón de Excel con un diseño personalizado
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Exportar a Excel', // Icono y texto
                className: 'btn btn-success btn-sm', // Estilo con Bootstrap
                titleAttr: 'Exportar a Excel'
            }
        ],
        "initComplete": function() {
            // Agregar clase adicional o estilo si es necesario
            $('.dt-buttons .btn').addClass('mx-2');
        }
    });
    

    // Evento para mostrar un mensaje emergente cuando se pase el mouse por encima de un botón
    $(document).on('mouseenter', '.btn', function () {
        $(this).tooltip('show');
    });

    $(document).on('mouseleave', '.btn', function () {
        $(this).tooltip('hide');
    });

    $('#solicitudesSecret').on('click', '.ver-solicitud', function () {
        cerrarSesion();
        const fileUrl = $(this).data('file-url');  // Obtener la URL del archivo
        console.log("Ver archivo con URL:", fileUrl);
    
        const id = $(this).data('file-url');  // Obtener el ID
        const estado = $(this).closest('tr').find('.badge').text().trim();  // Obtener el estado actual de la solicitud
        
        // Obtener columna2 y columna3 directamente desde el botón
        const columna2 = $(this).data('columna-2');  // Obtener columna2 desde el botón
        const columna3 = $(this).data('columna-3');  // Obtener columna3 desde el botón
        
        // Mostrar el modal
        modalSol(id);
    
        // Validar el estado antes de enviar la solicitud AJAX
        if (estado === "No Leído") {
            $.ajax({
                url: "../ajax/solicitud.php?op=cambiarEstado",
                type: "POST",
                data: { columna2, columna3, idEstado: 2 },  // Enviar columna2, columna3 e idEstado
                success: function (response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        console.log("Estado actualizado correctamente:", result.message);
                        obtId(columna2, columna3);
                        
                    } else {
                        console.error("Error al actualizar el estado:", result.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error en la solicitud AJAX:", error);
                }
            });
        } else {
            console.log("La acción no se puede realizar porque el estado no es 'No Leído'.");
        }
    });
    
    
    // Evento para ver otro dato
    $('#solicitudesSecret').on('click', '.ver-otro-dato', function () {
        const fileUrl = $(this).data('file-url');
        console.log("Ver archivo con URL:", fileUrl);
        const id = $(this).data('file-url');
        modalSol(id);
    });


    function obtId(columna2, columna3) {  // Corregido: sin '$' en los parámetros
        if (!columna2 || !columna3) {
            console.error("❌ Error: columna2 o columna3 no definidas.");
            return;
        }
    
        const data = {
            sol_sol: columna2,
            sol_doc: columna3
        };
    
        $.ajax({
            url: "../ajax/solicitud.php?op=idSolDoc",
            type: "POST",
            dataType: "json",
            data: data,
            success: function (response) {
                console.log("✅ Respuesta del servidor:", response);
                guardarSeguimiento();
            },
            error: function (xhr, status, error) {
                console.error("❌ Error en la solicitud AJAX:", error);
            }
        });
    }

    function obtIds() {  // Sin parámetros
        $.ajax({
            url: "../ajax/solicitud.php?op=idSolDoc", // Usando la operación para obtener desde la sesión
            type: "GET", // Usando GET ya que no enviamos datos
            dataType: "json",
            success: function (response) {
                console.log("✅ Respuesta del servidor:", response);
                if (response.success) {
                    guardarSeguimiento();  // Llamar a la función que deseas ejecutar
                } else {
                    console.error("❌ Error:", response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("❌ Error en la solicitud AJAX:", error);
            }
        });
    }
    
    function cerrarSesion() {
        // Realizar la solicitud AJAX para cerrar la sesión
        $.ajax({
          url: "../ajax/solicitud.php?op=cerrarSesion", // Dirección para cerrar sesión
          type: "GET",
          success: function (response) {
            
          },
          error: function (xhr, status, error) {
            // Manejo de errores al intentar cerrar sesión
            Swal.fire({
              title: "Error",
              text: "Error al cerrar sesión: " + error,
              icon: "error",
              confirmButtonText: "Aceptar",
            });
          },
        });
      }
    function guardarSeguimiento() {
        // Definir los datos del seguimiento
        const data = {
          OP: 2, // Operación: Indica que se está registrando un seguimiento
          seg_accion: "Solicitud Leída", // Acción registrada en el seguimiento
          seg_visto: 0, // Estado de visualización (0: No visto, 1: Visto)
          seg_comentario: "Su solicitud ha sido leída. Pronto recibirá respuesta sobre su aprobación. Manténgase atento."
        };
      
        // Enviar los datos al servidor mediante AJAX
        $.ajax({
          url: "../ajax/solicitud.php?op=InsertSeguimiento", // Ruta del servicio backend
          type: "POST",
          dataType: "json",
          data: data,
          success: function (response) { // Cierra la sesión después de guardar el seguimiento
            ('#solicitudesSecret').DataTable().ajax.reload();
            cerrarSesion();
          },
          error: function (jqXHR, textStatus, errorThrown) {
            Swal.fire({
              title: "Error",
              text: "No se pudo registrar el seguimiento. Inténtelo de nuevo.",
              icon: "error",
              confirmButtonText: "Aceptar"
            });
          }
        });
      }
      

    function agregarModal(content) {
        // Limpiar cualquier modal anterior
        $('.modal, #overlay').remove();

        // Agregar el nuevo modal al DOM
        $('body').append(content);

        // Mostrar el modal y overlay
        $('.modal').addClass('show');
        $('#overlay').addClass('show');
    }

    // Función para cerrar cualquier modal
    function cerrarModal() {
        $('#solicitudesSecret').DataTable().ajax.reload();
        $('.modal, #overlay').remove();
        
    }

    // Evento para manejar el modal de "Dejar un mensaje"
    $('#solicitudesSecret').on('click', '.cancelar-datos', function () {
        const columna2 = $(this).data('columna-2');
        const columna3 = $(this).data('columna-3');

        $.ajax({
            url: "../ajax/solicitud.php?op=modalSecretaria",
            type: "POST",
            data: { columna2, columna3 },
            success: function (response) {
                // Agregar el modal dinámicamente al DOM
                agregarModal(response);

                // Manejar eventos del modal
                $('#btnAprobar').on('click', function () {
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
                            obtId(columna2, columna3);
                            cerrarModal();
                        }
                    });
                });

                $('#btnRechazar').on('click', function () {
                    Swal.fire({
                        title: "Solicitud Rechazada",
                        text: "¿Estás seguro de proceder?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Sí, proceder",
                        cancelButtonText: "Cancelar",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            cambiarEstado('4')
                            $('#mensajeArea').show();
                            $('#mensaje').focus();
                        }
                    });
                });

                $('#btnEnviar').on('click', function () {
                    const mensaje = $('#mensaje').val().trim();
                    if (mensaje) {
                        Swal.fire("Mensaje enviado", "El mensaje se envió correctamente.", "success");
                    } else {
                        Swal.fire("Error", "Por favor, escribe un mensaje antes de enviar.", "error");
                    }
                });

                // Cerrar modal
                $('.modal-close, #overlay').on('click', cerrarModal);
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar el modal:", error);
            },
        });
    });

    // Evento para manejar el modal de visualización de documentos
    function modalSol(idDrive) {
        $.ajax({
            url: "../ajax/solicitud.php?op=modalSol",
            type: "POST",
            dataType: "json",
            data: { id: idDrive },
            success: function (response) {
                if (response.modalContent) {
                    agregarModal(response.modalContent);

                    // Evento para cerrar modal
                    $('#overlay, .modal-close').on('click', cerrarModal);
                } else {
                    Swal.fire("Error", "El contenido del modal no se pudo cargar.", "error");
                }
            },
            error: function () {
                Swal.fire("Error", "No se pudo cargar el modal. Intenta de nuevo.", "error");
            },
        });
    }

    function mostrarModalAprobar() {
        $.ajax({
            url: "../ajax/solicitud.php?op=modalAprobar",
            type: "GET",
            success: function (response) {
                // Crear un contenedor para el modal
                const modalContainer = $('<div>').html(response);
    
                // Usar agregarModal para mostrar el modal
                agregarModal(modalContainer);
    
                // Inicializar cualquier funcionalidad del modal (por ejemplo, agregar correos)
                inicializarAgregarCorreo();
            },
            error: function () {
                Swal.fire("Error", "No se pudo cargar el modal. Intenta de nuevo.", "error");
            }
        });
    }

    function cambiarEstado(idEstado){
        $.ajax({
            url: "../ajax/solicitud.php?op=cambiarEstado1",
            type: "POST",
            dataType: 'json',
            data: {id: idEstado},
            success: function(response){
                if (response.success) {
                    // Si el cambio de estado es exitoso, recargar la tabla
                    $('#solicitudesSecret').DataTable().ajax.reload();
                    alert(response.message); // Mostrar mensaje de éxito
                } else {
                    alert("Error: " + response.message); // Mostrar mensaje de error
                }
            },
            error: function(error){
                console.error("Error en la solicitud AJAX", error);
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
