function enviarSolicitudFormulario(formId, archivoInputId, submitBtnId, url) {
  $(document).ready(function () {
    $("#" + submitBtnId).on("click", function (e) {
      e.preventDefault();

      Swal.fire({
        title: "¿Estás seguro?",
        text: "¿Quieres enviar la solicitud?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, enviar",
        cancelButtonText: "No, cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          // Obtener el archivo
          var archivoInput = document.getElementById(archivoInputId);
          var archivo = archivoInput.files[0];

          // Crear objeto FormData
          var form = $("#" + formId);
          var formData = new FormData(form[0]);

          var btnSubmit = $("#" + submitBtnId);
          var originalText = btnSubmit.text();
          btnSubmit.prop("disabled", true).text("Procesando...");

          // Realizar solicitud AJAX
          $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
              try {
                var data = JSON.parse(response);
                if (data.estado) {
                  guardarSolicitud();
                  guardarSeguimiento();
                  
                  Swal.fire(
                    "¡Éxito!",
                    "La solicitud se ha enviado correctamente.",
                    "success"
                  );
                } else {
                  Swal.fire(
                    "Error",
                    "Hubo un problema al enviar la solicitud.",
                    "error"
                  );
                }
              } catch (e) {
                console.error("Error al procesar la respuesta:", e);
                Swal.fire(
                  "Error",
                  "No se pudo procesar la respuesta del servidor.",
                  "error"
                );
              }
            },
            error: function (xhr, status, error) {
              console.error("Error en la solicitud:", error);
              Swal.fire("Error", "No se pudo completar la solicitud.", "error");
            },
            complete: function () {
              btnSubmit.prop("disabled", false).text(originalText);
            },
          });
        }
      });
    });
  });
}

function guardarSolicitud() {
  // Obtener el valor del título correctamente usando el ID del elemento
  const titulo = document.getElementById("titulo").value;

  // Definir el estado como 5 (Enviado)
  const estado_id = 5;
  const asunto_id = document.getElementById("Tipo").value;

  // Verificar si el título está vacío
  if (!titulo.trim()) {
    Swal.fire({
      title: "Advertencia",
      text: "Debe ingresar un título.",
      icon: "warning",
      confirmButtonText: "Aceptar",
    });
    return; // Salir de la función si el título está vacío
  }

  // Realizar la solicitud AJAX al servidor para guardar la solicitud
  $.ajax({
    url: "../ajax/usuario.php?op=solicitud",
    type: "POST",
    dataType: "json",
    data: {
      estado_id: estado_id,
      titulo: titulo, 
      asunto_id: asunto_id
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
}

function guardarSeguimiento() {
  const OP = 2;
  const seg_accion = "Solicitud Enviada";
  const seg_visto = 0;
  const seg_comentario =
    "Su solicitud ha sido enviada correctamente. Debe esperar a que un responsable revise su solicitud para ser aprobada o rechazada. Manténgase atento.";

  const data = {
    OP: OP,
    seg_accion: seg_accion,
    seg_visto: seg_visto,
    seg_comentario: seg_comentario,
  };

  console.log("📤 Enviando datos al servidor:", data); // Mostrar en consola

  $.ajax({
    url: "../ajax/solicitud.php?op=InsertSeguimiento",
    type: "POST",
    dataType: "json",
    data: data,
    success: function (response) {
      console.log("✅ Respuesta del servidor:", response); // Mostrar la respuesta en consola
      cerrarSesion();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error(
        "❌ Error al guardar seguimiento:",
        textStatus,
        errorThrown
      );
    },
  });
}

function cerrarSesion() {
  // Realizar la solicitud AJAX para cerrar la sesión
  $.ajax({
    url: "../ajax/solicitud.php?op=cerrarSesion", // Dirección para cerrar sesión
    type: "GET",
    success: function (response) {},
    error: function (xhr, status, error) {
      // Manejo de errores al intentar cerrar sesión
      console.error("Error al cerrar sesión:", error); // Mostrar solo en consola
    },
  });
}

function obtenerE() {
    // Realizar la solicitud AJAX para cerrar la sesión
    $.ajax({
      url: "../ajax/usuario.php?op=ObtenerE",
      type: "GET",
      success: function (response) {},
      error: function (xhr, status, error) {
        // Manejo de errores al intentar cerrar sesión
        console.error("Error al cerrar sesión:", error); // Mostrar solo en consola
      },
    });
  }

$(document).ready(function () {
  enviarSolicitudFormulario(
    "formSolicitud", // El ID del formulario
    "archivo", // El ID del input tipo file
    "submitBtn", // El ID del botón de submit
    "../ajax/solicitud.php?op=estado" // URL del controlador que manejará la solicitud
  );
  obtenerE();
  llenarSelect();
});

function llenarSelect(){
  $.ajax({
        url: '../ajax/solicitud.php?op=obtAsunto',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            let select = $('#Tipo');
            select.empty(); // Limpia opciones anteriores
            select.append('<option value="">Seleccione el tipo de solicitud</option>');
            data.forEach(function (asunto) {
                select.append(
                    '<option value="' + asunto.asunto_id + '">' + asunto.asunto_nombre + '</option>'
                );
            });
        },
        error: function () {
            alert('Error al cargar los asuntos.');
        }
    });

}