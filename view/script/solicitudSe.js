function cerrarModal() {
    // Realiza una solicitud AJAX para cerrar la sesión
    $.ajax({
        url: "../ajax/solicitud.php?op=cerrarSesion",
        type: "POST",
        success: function (response) {
            console.log(response); // Mensaje de confirmación en la consola
            // Oculta el modal y el overlay
            document.getElementById("modalSubir").style.display = "none";
            document.getElementById("overlay").style.display = "none";
        },
        error: function (xhr, status, error) {
            console.error("Error al cerrar la sesión:", error);
        },
    });
}


function mostrarModalAprobar() {
    document.getElementById("modalEnviarSolicitud").style.display = "block";
    document.getElementById("overlay").style.display = "block";
}

