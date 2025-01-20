function cerrarModalAprobar() {
    document.getElementById("modalEnviarSolicitud").style.display = "none";
    document.getElementById("overlay").style.display = "none";
}

$(document).ready(function() {
    const columna2 = localStorage.getItem('columna2');
    const columna3 = localStorage.getItem('columna3');

    if (columna2 && columna3) {
        console.log('Columna 2:', columna2);
        console.log('Columna 3:', columna3);

        // Usar los valores en tu formulario o lógica
        $('#inputColumna2').val(columna2);
        $('#inputColumna3').val(columna3);
    } else {
        console.error('No se encontraron valores de las columnas en localStorage.');
    }

    // Limpiar localStorage después de usar los datos
    localStorage.removeItem('columna2');
    localStorage.removeItem('columna3');
    // Obtener el valor del primer combo (motivo de la solicitud)
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
    
        const columna2 = $('#inputColumna2').val();
        const columna3 = $('#inputColumna3').val();
    
        const data = {
            motivoSolicitud: motivoSolicitud,
            correosSeleccionados: correosSeleccionados,
            comentario: comentario,
            columna2: columna2, // Añadido
            columna3: columna3  // Añadido
        };
    
        // Enviar los datos con AJAX usando jQuery
        $.ajax({
            url: '../ajax/phpMailer.php?op=enviarCorreo',
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(response) {
                if (response.estado) {
                    alert(response.mensaje); // Mensaje de éxito
                } else {
                    alert(response.error); // Mensaje de error
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Hubo un error al enviar la solicitud.');
            }
        });
    }
    

    // Agregar el evento de envío al formulario
    $('#formEnviarSolicitud').on('submit', function(event) {
        event.preventDefault(); // Prevenir el envío por defecto
        enviarSolicitud(); // Llamar a la función para enviar la solicitud
    });
});



