function cerrarModalAprobar() {
  $.ajax({
    url: "../ajax/solicitud.php?op=cerrarSesion",
    type: "POST",
    success: function (response) {
      console.log(response); // Mensaje de confirmación en la consola

      // Usar jQuery para ocultar el modal y el overlay
      $("#modalEnviarSolicitud").remove();
      $("#overlay").remove();
    },
    error: function (xhr, status, error) {
      console.error("Error al cerrar la sesión:", error);
    },
  });
}

$(document).ready(function () {
  const motivoSolicitud = $("#motivoSolicitud").val();

  // Función para obtener los correos seleccionados
  function obtenerCorreosSeleccionados() {
    const correosSeleccionados = [];
    $("#correosDestinatarios select").each(function () {
      const correoSeleccionado = $(this).val();
      if (correoSeleccionado && correoSeleccionado !== "") {
        correosSeleccionados.push(correoSeleccionado);
      }
    });
    return correosSeleccionados;
  }

  // Función para enviar la solicitud con AJAX
  function enviarSolicitud() {
    const correosSeleccionados = obtenerCorreosSeleccionados();
    const comentarioDestinatario = $("#comentarioDestinatario").val(); // Comentario para el destinatario
    const motivoSolicitud = $("#motivoSolicitud").val(); // Obtener el motivo seleccionado

    // Validación de campos
    if (!motivoSolicitud) {
      swal.fire({
        icon: "warning",
        title: "Atención",
        text: "Por favor selecciona un motivo de solicitud.",
        confirmButtonText: "Aceptar",
      });
      return;
    }

    if (correosSeleccionados.length === 0) {
      swal.fire({
        icon: "warning",
        title: "Atención",
        text: "Por favor selecciona al menos un correo.",
        confirmButtonText: "Aceptar",
      });
      return;
    }

    if (!comentarioDestinatario) {
      swal.fire({
        icon: "warning",
        title: "Atención",
        text: "Por favor ingresa un comentario para el destinatario.",
        confirmButtonText: "Aceptar",
      });
      return;
    }

    // Si pasa la validación, construye el objeto de datos
    const data = {
      motivoSolicitud: motivoSolicitud,
      correosSeleccionados: correosSeleccionados,
      comentario: comentarioDestinatario, // Comentario obligatorio para el destinatario
    };

    // Llamada AJAX para enviar datos al servidor
    $.ajax({
      url: "../ajax/phpMailer.php?op=enviarCorreo",
      type: "POST",
      dataType: "json",
      data: data,
      success: function (response) {
        if (response.estado) {
          swal.fire({
            icon: "success",
            title: "Éxito",
            text: response.mensaje,
            confirmButtonText: "Aceptar",
          });
          
          cambiarEstado(3);
          guardarSeguimiento();
        } else {
          swal.fire({
            icon: "error",
            title: "Error",
            text: response.error,
            confirmButtonText: "Intentar nuevamente",
          });
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
        swal.fire({
          icon: "error",
          title: "Error de conexión",
          text: "Hubo un error al enviar la solicitud.",
          confirmButtonText: "Aceptar",
        });
      },
    });
  }

  function obtId() {
    // Sin parámetros
    $.ajax({
      url: "../ajax/solicitud.php?op=idSolDoc", // Usando la operación para obtener desde la sesión
      type: "GET", // Usando GET ya que no enviamos datos
      dataType: "json",
      success: function (response) {
        console.log("✅ Respuesta del servidor:", response);
        if (response.success) {
          guardarSeguimiento(); // Llamar a la función que deseas ejecutar
        } else {
          console.error("❌ Error:", response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("❌ Error en la solicitud AJAX:", error);
      },
    });
  }

  // Agregar el evento de envío al formulario
  $("#formEnviarSolicitud").on("submit", function (event) {
    event.preventDefault(); // Prevenir el envío por defecto
    enviarSolicitud(); // Llamar a la función para enviar la solicitud
  });
});

function guardarSeguimiento() {
  const comentarioEstudiante = $("#comentarioEstudiante").val(); // Comentario del estudiante

  const data = {
    OP: 2, // Operación: Indica que se está registrando un seguimiento
    seg_accion: "Solicitud Aprobada", // Acción registrada en el seguimiento
    seg_visto: 0, // Estado de visualización (0: No visto, 1: Visto)
    seg_comentario: comentarioEstudiante,
  };

  // Enviar los datos al servidor mediante AJAX
  $.ajax({
    url: "../ajax/solicitud.php?op=InsertSeguimiento", // Ruta del servicio backend
    type: "POST",
    dataType: "json",
    data: data,
    success: function (response) {
      // Cierra la sesión después de guardar el seguimiento
    },
    error: function (jqXHR, textStatus, errorThrown) {
      Swal.fire({
        title: "Error",
        text: "No se pudo registrar el seguimiento. Inténtelo de nuevo.",
        icon: "error",
        confirmButtonText: "Aceptar",
      });
    },
  });
}

function cambiarEstado(idEstado) {
  $.ajax({
    url: "../ajax/solicitud.php?op=cambiarEstado1",
    type: "POST",
    dataType: "json",
    data: { id: idEstado },
    success: function (response) {
      if (response.success) {
        // Si el cambio de estado es exitoso, recargar la tabla
        $("#solicitudesSecret").DataTable().ajax.reload();
        //alert(response.message); // Mostrar mensaje de éxito
      } else {
        alert("Error: " + response.message); // Mostrar mensaje de error
      }
    },
    error: function (error) {
      console.error("Error en la solicitud AJAX", error);
    },
  });
}
