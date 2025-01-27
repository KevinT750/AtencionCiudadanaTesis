function cerrarModalAprobar() {
    $.ajax({
        url: "../ajax/solicitud.php?op=cerrarSesion",
        type: "POST",
        success: function (response) {
            console.log(response); // Mensaje de confirmación en la consola

            // Usar jQuery para ocultar el modal y el overlay
            $('#modalEnviarSolicitud').remove();
            $('#overlay').remove();
        },
        error: function (xhr, status, error) {
            console.error("Error al cerrar la sesión:", error);
        },
    });
}


$(document).ready(function() {

    const motivoSolicitud = $('#motivoSolicitud').val();

    // Función para obtener los correos seleccionados
    function obtenerCorreosSeleccionados() {
        const correosSeleccionados = [];
        $('#correosDestinatarios select').each(function() {
            const correoSeleccionado = $(this).val();
            if (correoSeleccionado && correoSeleccionado !== '') {
                correosSeleccionados.push(correoSeleccionado);
            }
        });
        return correosSeleccionados;
    }

    // Función para enviar la solicitud con AJAX
    function enviarSolicitud() {
        const correosSeleccionados = obtenerCorreosSeleccionados();
        const comentario = $('#comentario').val();
    
        // Validación de campos
        if (!motivoSolicitud) {
            swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor selecciona un motivo de solicitud.',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
    
        if (correosSeleccionados.length === 0) {
            swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor selecciona al menos un correo.',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
    
        if (!comentario) {
            swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor ingresa un comentario.',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
    
        // Si pasa la validación, construye el objeto de datos
        const data = {
            motivoSolicitud: motivoSolicitud,
            correosSeleccionados: correosSeleccionados,
            comentario: comentario,
        };
    
        // Llamada AJAX para enviar datos al servidor
        $.ajax({
            url: '../ajax/phpMailer.php?op=enviarCorreo',
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(response) {
                if (response.estado) {
                    swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.mensaje,
                        confirmButtonText: 'Aceptar'
                    });
                    cambiarEstado(3);
                } else {
                    swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error,
                        confirmButtonText: 'Intentar nuevamente'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Hubo un error al enviar la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    }
    

    // Agregar el evento de envío al formulario
    $('#formEnviarSolicitud').on('submit', function(event) {
        event.preventDefault(); // Prevenir el envío por defecto
        enviarSolicitud(); // Llamar a la función para enviar la solicitud
    });
});

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


