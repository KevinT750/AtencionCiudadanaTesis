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
              guardarSolicitud();
              guardarSeguimiento();
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
    // Obtener los valores de las sesiones
    const cedula_id = sessionStorage.getItem("cedula_ids");
    const doc_id = sessionStorage.getItem("doc_ids");
    const est_id = sessionStorage.getItem("usu_id");

    // El estado será 5 (Enviado) en este caso
    const estado_id = 5;

    // Realizar la solicitud AJAX al servidor para guardar la solicitud
    $.ajax({
      url: "../ajax/usuario.php?op=solicitud",
      type: "POST",
      dataType: "json",
      data: {
        cedula_id: cedula_id,
        doc_id: doc_id,
        est_id: est_id,
        estado_id: estado_id, // Enviamos el estado aquí
      },
      success: function (response) {
        // Manejo de la respuesta
        if (response.success) {
          Swal.fire({
            title: "Éxito",
            text: "Solicitud procesada correctamente.",
            icon: "success",
            confirmButtonText: "Aceptar",
          }).then(function () {});
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
    success: function (response) {
      Swal.fire({
        title: "Sesión cerrada",
        text: response, // Mensaje que se recibe al cerrar sesión
        icon: "success",
        confirmButtonText: "Aceptar",
      }).then(function () {
        // Redirigir a la página principal o logout
        // window.location.href = "login.php"; // O la URL que necesites
      });
    },
    error: function (xhr, status, error) {
      // Manejo de errores al intentar cerrar sesión
      Swal.fire({
        title: "Error",
        text: "Error al cerrar sesión: " + error,
        icon: "error",
        confirmButtonText: "Aceptar",
      });
    },
  });
}

/*function cargarSolicitudes() {
    $.ajax({
      url: "../ajax/usuario.php?op=estado",
      type: "GET",
      dataType: "json",
      success: function (response) {
        if (response.success) {
          const solicitudes = response.solicitudes;
          let rows = "";
          solicitudes.forEach((solicitud) => {
            rows += `<tr>
                                  <td>${solicitud.sol_id}</td>
                                  <td>${solicitud.sol_fecha}</td>
                                  <td>
                                      <button class="btn btn-info btn-sm" onclick="verSolicitud(${solicitud.sol_solicitud})">
                                          <i class="fa fa-eye"></i> Ver Solicitud
                                      </button>
                                  </td>
                                  <td>
                                      <button class="btn btn-success btn-sm" onclick="verDocumento(${solicitud.sol_documento})">
                                          <i class="fa fa-file-pdf"></i> Ver Documento
                                      </button>
                                  </td>
                                  <td><span class="badge bg-${solicitud.estado_class}">${solicitud.estado_nombre}</span></td>
                                </tr>`;
          });
          $("#solicitudesTable tbody").html(rows);
        } else {
          alert(response.error);
        }
      },
      error: function () {
        alert("Error al obtener las solicitudes");
      },
    });
  }
  
*/
$(document).ready(function () {
  enviarSolicitudFormulario(
    "formSolicitud", // El ID del formulario
    "archivo", // El ID del input tipo file
    "submitBtn", // El ID del botón de submit
    "../ajax/solicitud.php?op=estado" // URL del controlador que manejará la solicitud
  );
  //cargarSolicitudes();
});
