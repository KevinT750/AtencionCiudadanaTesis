$(document).ready(function () {
    // Búsqueda por nombre
    $('#nombre').on('keyup', function () {
        let valor = $(this).val();
        if (valor.length > 0) {
            $.ajax({
                url: '../ajax/usuario.php?op=estudianteId',
                method: 'POST',
                data: {
                    tipo: 'nombre',
                    valor: valor,
                    accion: 'estudianteId'
                },
                success: function (respuesta) {
                    let resultados = JSON.parse(respuesta);
                    let html = '';
                    if (resultados.length > 0) {
                        resultados.forEach(function (resultado) {
                            html += `<li class="list-item" 
                                         data-id="${resultado.id}" 
                                         data-nombre="${resultado.nombre}" 
                                         data-cedula="${resultado.cedula}">
                                ${resultado.nombre} - ${resultado.cedula}
                            </li>`;
                        });
                    } else {
                        html = '<li>Sin resultados</li>';
                    }
                    $('#resultados-nombre').html(html).show();
                }
            });
        } else {
            $('#resultados-nombre').hide();
        }
    });

    // Búsqueda por cédula
    $('#cedula').on('keyup', function () {
        let valor = $(this).val();
        if (valor.length > 0) {
            $.ajax({
                url: '../ajax/usuario.php?op=estudianteId',
                method: 'POST',
                data: {
                    tipo: 'cedula',
                    valor: valor,
                    accion: 'estudianteId'
                },
                success: function (respuesta) {
                    let resultados = JSON.parse(respuesta);
                    let html = '';
                    if (resultados.length > 0) {
                        resultados.forEach(function (resultado) {
                            html += `<li class="list-item" 
                                         data-id="${resultado.id}" 
                                         data-nombre="${resultado.nombre}" 
                                         data-cedula="${resultado.cedula}">
                                ${resultado.cedula} - ${resultado.nombre}
                            </li>`;
                        });
                    } else {
                        html = '<li>Sin resultados</li>';
                    }
                    $('#resultados-cedula').html(html).show();
                }
            });
        } else {
            $('#resultados-cedula').hide();
        }
    });

    // Seleccionar un resultado de la lista
    $(document).on('click', '.list-item', function () {
        const id = $(this).data('id');  // Obtener la ID
        let nombre = $(this).data('nombre');  // Obtener el nombre
        let cedula = $(this).data('cedula');  // Obtener la cédula
    
        // Verificar que la ID está correcta
        console.log('ID seleccionada: ', id);
    
        // Rellenar los campos de texto
        $('#nombre').val(nombre);
        $('#cedula').val(cedula);
    
        // Guardar la ID seleccionada en un atributo de datos o campo oculto
        $('#formSubirSolicitud').data('id', id);
    
        // Ocultar las listas de resultados
        $('.list-group').hide();
    });
    

    // Ocultar la lista si se hace clic fuera
    $(document).click(function (event) {
        if (!$(event.target).closest('.form-group').length) {
            $('.list-group').hide();
        }
    });

    function guardarSolicitud() {
        // Verificar si las sesiones están configuradas (simulación en el lado del cliente)
        if (
            typeof sessionStorage.getItem("doc_ids") !== "undefined" &&
            typeof sessionStorage.getItem("cedula_ids") !== "undefined"
        ) {
            // Obtener los valores de las sesiones
            const cedula_id = sessionStorage.getItem("cedula_ids");
            const doc_id = sessionStorage.getItem("doc_ids");
            
            // Obtener la ID seleccionada en el formulario
            const est_id = $('#formSubirSolicitud').data('id');
    
            // El estado será 6 (Documentos subidos) en este caso
            const estado_id = 6;
    
            // Realizar la solicitud AJAX al servidor para guardar la solicitud en la base de datos
            $.ajax({
                url: "../ajax/usuario.php?op=solicitud1",
                type: "POST",
                dataType: "json",
                data: {
                    cedula_id: cedula_id,
                    doc_id: doc_id,
                    est_id: est_id,  // Enviamos la ID seleccionada
                    estado_id: estado_id,  // Enviamos el estado aquí
                },
                success: function (response) {
                    // Manejo de la respuesta
                    if (response.success) {
                        Swal.fire({
                            title: "Éxito",
                            text: "Solicitud procesada correctamente.",
                            icon: "success",
                            confirmButtonText: "Aceptar",
                        });
                        cerrarSesion();
                        cargarSolicitudes(); // Cargar las solicitudes después de guardar la nueva
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.error || "No se pudo procesar la solicitud.",
                            icon: "error",
                            confirmButtonText: "Aceptar",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    // Manejo de errores
                    Swal.fire({
                        title: "Error",
                        text: "Error en el servidor: " + error,
                        icon: "error",
                        confirmButtonText: "Aceptar",
                    });
                },
            });
        } else {
            Swal.fire({
                title: "Advertencia",
                text: "No hay información suficiente en la sesión.",
                icon: "warning",
                confirmButtonText: "Aceptar",
            });
        }
    }

    // Función para enviar la solicitud con archivos a Google Drive o similar
    function enviarSolicitud(event) {
        // Prevenir el envío por defecto del formulario
        event.preventDefault();

        // Obtener los datos del formulario
        const nombre = document.getElementById('nombre').value;
        const cedula = document.getElementById('cedula').value;
        const archivoSolicitud = document.getElementById('archivo_solicitud').files[0];
        const archivoCedula = document.getElementById('archivo_cedula').files[0];

        // Crear un objeto FormData para enviar los datos y archivos
        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('cedula', cedula);
        formData.append('archivo_solicitud', archivoSolicitud);
        formData.append('archivo_cedula', archivoCedula);

        // Usar AJAX para enviar los datos al servidor
        $.ajax({
            url: '../ajax/solicitud.php?op=estado1',
            type: 'POST',
            data: formData,
            contentType: false,  // Importante para enviar archivos
            processData: false,  // Impide que jQuery procese los datos como string
            success: function (data) {
                if (data.estado) {
                    alert('Solicitud enviada correctamente');
                    // Realiza las acciones necesarias si la solicitud fue exitosa, por ejemplo, limpiar el formulario
                    document.getElementById('formSubirSolicitud').reset();
                    // Llamar a guardarSolicitud después de que se haya enviado correctamente
                    guardarSolicitud();
                } else {
                    alert('Error al enviar la solicitud: ' + data.error);
                    document.getElementById('formSubirSolicitud').reset();
                    guardarSolicitud();
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('Hubo un problema al procesar la solicitud.');
            }
        });
    }

    // Asignar el evento 'submit' al formulario para llamar a la función enviarSolicitud
    document.getElementById('formSubirSolicitud').addEventListener('submit', function (e) {
        enviarSolicitud(e);  // Primero enviar la solicitud
    });
});
