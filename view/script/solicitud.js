document.getElementById("submitBtn").addEventListener("click", function(event) {
    event.preventDefault();  // Prevenir que el formulario se envíe de forma normal

    var form = document.getElementById("formSolicitud");
    var formData = new FormData(form);
    console.log(formData);  // Verifica qué datos se están enviando


    var xhr = new XMLHttpRequest();
    xhr.open("POST", "guardar_con_datos.php", true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            alert("Solicitud enviada exitosamente.");
            // Puedes agregar un redireccionamiento si es necesario
            // window.location.href = 'somepage.php';
        } else {
            alert("Error al enviar la solicitud.");
        }
    };

    xhr.send(formData);
});
