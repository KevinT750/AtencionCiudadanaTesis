$(document).ready(function() {
    $("#submitBtn").on('click', function(e) {
        e.preventDefault();

        var archivoInput = document.getElementById("archivo");
        var archivo = archivoInput.files[0];

        if (!archivo) {
            alert("Por favor, adjunte un archivo en formato PDF.");
            return;
        }

        if (archivo.size > 2 * 1024 * 1024) {
            alert("El archivo excede el tamaño máximo permitido de 2 MB.");
            return;
        }

        if (archivo.type !== "application/pdf") {
            alert("Solo se permiten archivos en formato PDF.");
            return;
        }

        var form = $("#formSolicitud"); // Usar selector jQuery
        var formData = new FormData(form[0]); // Obtener el elemento DOM del formulario

        var btnSubmit = $(this); // $(this) se refiere al botón que se hizo clic
        var originalText = btnSubmit.text();
        btnSubmit.prop('disabled', true).text('Procesando...');

        $.ajax({
            url: "../ajax/solicitud.php", // Enviar al controlador
            type: "POST",
            data: formData,
            processData: false, // Importantísimo para FormData
            contentType: false, // Importantísimo para FormData
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    if (data.estado) {
                        alert("Solicitud enviada correctamente.");
                        form[0].reset(); // Limpiar el formulario (acceder al elemento DOM)
                    } else {
                        alert("Error al enviar la solicitud: " + data.error);
                        console.error("Error del servidor:", data.error);
                    }
                } catch (e) {
                    alert("Error al procesar la respuesta del servidor.");
                    console.error("Error al parsear JSON:", e, response); // Mostrar la respuesta completa para debugging
                }
            },
            error: function(xhr, status, error) {
                alert("Error en la conexión: " + error);
                console.error("Error en la solicitud:", status, error, xhr.responseText);
            },
            complete: function() {
                btnSubmit.prop('disabled', false).text(originalText);
            }
        });
    });
});