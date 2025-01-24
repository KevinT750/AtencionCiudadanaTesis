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
        const data = {
            motivoSolicitud: motivoSolicitud,
            correosSeleccionados: correosSeleccionados,
            comentario: comentario,
        };
    
        // Enviar los datos con AJAX usando jQuery
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




