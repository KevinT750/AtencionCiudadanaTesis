// Abrir el modal
function abrirModal(url) {
  var modal = document.getElementById('miModal');
  var iframe = document.getElementById('iframeArchivo');
  iframe.src = url;  // Asignar el URL del archivo al iframe
  modal.style.display = "block";
}

// Cerrar el modal
document.getElementById('closeModal').addEventListener('click', function () {
  var modal = document.getElementById('miModal');
  modal.style.display = "none";
  var iframe = document.getElementById('iframeArchivo');
  iframe.src = "";  // Limpiar el iframe cuando se cierra el modal
});

// Cerrar el modal si se hace clic fuera de él
window.addEventListener('click', function (event) {
  var modal = document.getElementById('miModal');
  if (event.target === modal) {
      modal.style.display = "none";
      var iframe = document.getElementById('iframeArchivo');
      iframe.src = "";  // Limpiar el iframe cuando se cierra el modal
  }
});

// Función para enviar la solicitud del formulario
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

// Función para guardar la solicitud
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

// Función para cargar las solicitudes
function cargarSolicitudes() {
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
            <button 
              class="btn btn-info btn-sm" 
              data-bs-toggle="modal" 
              data-bs-target="#modalArchivo" 
              data-file-id="${solicitud.sol_solicitud}">
              <i class="fa fa-eye"></i> Ver Solicitud
            </button>
          </td>
          <td>
            <button 
              class="btn btn-success btn-sm" 
              data-bs-toggle="modal" 
              data-bs-target="#modalArchivo" 
              data-file-id="${solicitud.sol_documento}">
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

// Función para manejar los modales
function modalSolicitud() {
// Manejar clic en los botones de la tabla
$("#solicitudesTable").on("click", ".btn-info, .btn-success", function () {
  var fileId = $(this).data("file-id");

  // Realizar solicitud AJAX para obtener la información del archivo
  $.ajax({
    url: "../ajax/usuario.php?op=verDocumento&id=" + fileId,
    type: "GET",
    dataType: "json",
    success: function (response) {
      if (response.estado) {
        var fileId = response.archivo.id;
        var fileType = response.archivo.nombre.split('.').pop(); // Extraer la extensión del archivo

        // Dependiendo del tipo de archivo, mostrarlo en el iframe
        var iframeSrc = "";
        if (fileType === 'pdf') {
          iframeSrc = "https://drive.google.com/file/d/" + fileId + "/view";
        } else if (fileType === 'docx') {
          iframeSrc = "https://docs.google.com/document/d/" + fileId + "/edit";
        }

        // Cargar el archivo en el iframe
        $("#iframeArchivo").attr("src", iframeSrc);

        // Mostrar el modal
        var modal = document.getElementById("miModal");
        modal.style.display = "block";
      } else {
        Swal.fire({
          title: "Error",
          text: "No se pudo cargar el archivo.",
          icon: "error",
          confirmButtonText: "Aceptar",
        });
      }
    },
    error: function () {
      Swal.fire({
        title: "Error",
        text: "Error al cargar el archivo.",
        icon: "error",
        confirmButtonText: "Aceptar",
      });
    }
  });
});

// Cerrar el modal cuando se haga clic en el botón de cerrar
var span = document.getElementsByClassName("close")[0];
span.onclick = function () {
  var modal = document.getElementById("miModal");
  modal.style.display = "none";
};

// Cerrar el modal cuando se haga clic fuera del modal
window.onclick = function (event) {
  var modal = document.getElementById("miModal");
  if (event.target == modal) {
    modal.style.display = "none";
  }
};
}

$(document).ready(function () {
enviarSolicitudFormulario(
  "formSolicitud", // El ID del formulario
  "archivo", // El ID del input tipo file
  "submitBtn", // El ID del botón de submit
  "../ajax/solicitud.php" // URL del controlador que manejará la solicitud
);
cargarSolicitudes();
modalSolicitud();
});
