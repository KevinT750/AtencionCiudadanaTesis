
function enviarSolicitudFormulario(formId, archivoInputId, submitBtnId, url) {
  $(document).ready(function () {
    $("#" + submitBtnId).on("click", function (e) {
      e.preventDefault();

      // Obtener el archivo
      var archivoInput = document.getElementById(archivoInputId);
      var archivo = archivoInput.files[0];

      // Crear objeto FormData
      var form = $("#" + formId); // Usar selector jQuery
      var formData = new FormData(form[0]); // Obtener el elemento DOM del formulario

      var btnSubmit = $("#" + submitBtnId); // Referencia al botón
      var originalText = btnSubmit.text();
      btnSubmit.prop("disabled", true).text("Procesando...");

      // Realizar solicitud AJAX
      $.ajax({
        url: url, // URL del controlador
        type: "POST",
        data: formData,
        processData: false, // Importante para FormData
        contentType: false, // Importante para FormData
        success: function (response) {
          try {
            var data = JSON.parse(response); // Intentar parsear JSON
            if (data.estado) {
              guardarSolicitud(); // Llamar a guardarSolicitud después del éxito
            }
          } catch (e) {
            console.error("Error al procesar la respuesta:", e);
          }
        },
        error: function (xhr, status, error) {
          console.error("Error en la solicitud:", error);
        },
        complete: function () {
          btnSubmit.prop("disabled", false).text(originalText); // Restaurar botón
        },
      });
    });
  });
}

function guardarSolicitud() {
  // Verificar si las sesiones están configuradas (simulación en el lado del cliente)
  if (
    typeof sessionStorage.getItem("doc_ids") !== "undefined" &&
    typeof sessionStorage.getItem("cedula_ids") !== "undefined" &&
    typeof sessionStorage.getItem("usu_id") !== "undefined"
  ) {
    // Realizar la solicitud AJAX al servidor para guardar la solicitud
    $.ajax({
      url: "../ajax/usuario.php?op=solicitud",
      type: "POST",
      dataType: "json",
      success: function (response) {
        // Manejo de la respuesta
        if (response.success) {
          Swal.fire({
            title: "Éxito",
            text: "Solicitud procesada correctamente.",
            icon: "success",
            confirmButtonText: "Aceptar",
          });
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

$(document).ready(function () {
  enviarSolicitudFormulario(
    "formSolicitud", // El ID del formulario
    "archivo", // El ID del input tipo file
    "submitBtn", // El ID del botón de submit
    "../ajax/solicitud.php?op=estado" // URL del controlador que manejará la solicitud
  );
});