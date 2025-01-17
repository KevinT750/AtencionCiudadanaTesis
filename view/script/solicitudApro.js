document.addEventListener("DOMContentLoaded", function () {
    const agregarCorreoBtn = document.getElementById("agregarCorreo");
    const buscarCorreoInput = document.getElementById("buscarCorreo");
    const correosDestinatariosDiv = document.getElementById("correosDestinatarios");
    const formEnviarSolicitud = document.getElementById("formEnviarSolicitud");
    const errorCorreo = document.getElementById("errorCorreo");

    const correosDisponibles = [
        "coordinador1@universidad.edu",
        "coordinador2@universidad.edu",
        "coordinador3@universidad.edu"
    ];

    agregarCorreoBtn.addEventListener("click", function () {
        const correoSeleccionado = buscarCorreoInput.value.trim();

        if (correoSeleccionado && correosDisponibles.includes(correoSeleccionado)) {
            const divCorreo = document.createElement("div");
            divCorreo.classList.add("input-group", "mb-2");
            divCorreo.innerHTML = `
                <input type="text" class="form-control" value="${correoSeleccionado}" disabled>
                <button class="btn btn-danger btn-sm" type="button" onclick="eliminarCorreo(this)">X</button>
            `;
            correosDestinatariosDiv.appendChild(divCorreo);
            buscarCorreoInput.value = "";
            errorCorreo.style.display = "none";
        } else {
            errorCorreo.style.display = "block";
        }
    });

    window.eliminarCorreo = function (button) {
        button.parentElement.remove();
    };

    formEnviarSolicitud.addEventListener("submit", function (e) {
        e.preventDefault();
        const correosDestinatarios = [];
        const comentarios = document.getElementById("comentario").value.trim();

        const inputsCorreos = correosDestinatariosDiv.querySelectorAll("input");
        inputsCorreos.forEach(input => {
            correosDestinatarios.push(input.value);
        });

        if (correosDestinatarios.length > 0) {
            alert(`La solicitud ha sido enviada a: ${correosDestinatarios.join(", ")}`);
            formEnviarSolicitud.reset();
            correosDestinatariosDiv.innerHTML = "";
        } else {
            alert("Por favor, agrega al menos un correo destinatario.");
        }
    });

    function cerrarModalAprobar() {
        const modal = document.getElementById("modalEnviarSolicitud");
        if (modal) {
            modal.remove(); // Elimina el modal dinámico
        }
        document.getElementById("overlay").style.display = "none";
    }
});