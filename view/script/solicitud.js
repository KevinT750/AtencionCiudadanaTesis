document.getElementById("submitBtn").addEventListener("click", function(event) {
    event.preventDefault();  

    var archivoInput = document.getElementById("archivo");
    var archivo = archivoInput.files[0];

    // Validar que se haya seleccionado un archivo
    if (!archivo) {
        alert("Por favor, adjunte un archivo en formato PDF.");
        return;
    }

    // Validar el tamaño del archivo
    if (archivo.size > 2 * 1024 * 1024) { // 2 MB
        alert("El archivo excede el tamaño máximo permitido de 2 MB.");
        return;
    }

    // Validar el tipo del archivo
    if (archivo.type !== "application/pdf") {
        alert("Solo se permiten archivos en formato PDF.");
        return;
    }

    var form = document.getElementById("formSolicitud");
    var formData = new FormData(form);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "guardar_con_datos.php", true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            alert("Solicitud enviada correctamente.");
        } else {
            alert("Error al enviar la solicitud.");
            console.error(xhr.responseText);
        }
    };

    xhr.send(formData);
});
