$(document).ready(function () {
  function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }

  // Obtener el valor de 'sol_id' desde la URL
  const solicitudId = getQueryParam("sol_id");

  if (solicitudId) {
    console.log("sol_id obtenido:", solicitudId); // Para depuración
    obtenerSolicitudes(solicitudId); // Llamar la función con el ID obtenido
  } else {
    console.error("Error: sol_id no encontrado en la URL");
  }

  // Modificar la función para aceptar el ID
  function obtenerSolicitudes(sol_id) {
    $.ajax({
      url: "../ajax/solicitud.php?op=obSolSegEst", // Asegurar que el parámetro se envía
      type: "GET",
      data: { sol_id: sol_id }, // Pasar sol_id como un parámetro de la consulta
      dataType: "json",
      success: function (response) {
        if (response.error) {
          console.warn("Error en la respuesta:", response.error);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: response.error,
          });
          return;
        }
        mostrarSolicitudes(response);
      },
      error: function (xhr, status, error) {
        console.error("Error al obtener solicitudes:", error);
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Hubo un problema al obtener las solicitudes. Inténtalo de nuevo.",
        });
      },
    });
  }

  function mostrarSolicitudes(solicitudes) {
    let lista = $("#listaSolicitudes");
    lista.empty();

    solicitudes.forEach((solicitud) => {
      let item = $(`
        <div class="card mb-4 shadow-lg border-0 solicitud-item" data-id="${solicitud.sol_id}">
          <div class="card-header d-flex justify-content-between align-items-center bg-gradient-primary text-white">
            <h5 class="mb-0 font-weight-bold">
              <i class="fa fa-file-alt"></i> ${solicitud.titulo}
            </h5>
            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
              <i class="fa fa-calendar-alt"></i> ${solicitud.sol_fecha}
            </span>
          </div>
          <div class="card-body text-center">
            <button class="btn btn-ver px-4 py-2">
              <i class="fa fa-eye"></i> Ver Seguimiento
            </button>
            <div class="seguimiento mt-3 p-3" style="display: none;"></div>
          </div>
        </div>
      `);

      item.find(".btn-ver").click(function () {
        let seguimientoDiv = item.find(".seguimiento");

        if (seguimientoDiv.is(":empty")) {
          obtenerSeguimiento(solicitudId, seguimientoDiv);
        } else {
          seguimientoDiv.slideToggle();
        }
      });

      lista.append(item);
    });
  }

  function obtenerSeguimiento(sol_id, seguimientoDiv) {
    $.ajax({
      url: "../ajax/solicitud.php?op=obtenerSeg",
      type: "POST",
      data: { sol_id: sol_id },
      dataType: "json",
      success: function (data) {
        if (data.error) {
          seguimientoDiv.html(
            `<div class="alert alert-danger">${data.error}</div>`
          );
          return;
        }

        let detalles = '<ul class="list-group">';
        data.forEach((seg) => {
          let accionClase = "";
          let icono = "";

          switch (seg.seg_accion) {
            case "Solicitud Enviada":
              accionClase = "solicitud-enviada";
              icono = '<i class="fa fa-paper-plane"></i>';
              break;
            case "Solicitud Leída":
              accionClase = "solicitud-leida";
              icono = '<i class="fa fa-eye"></i>';
              break;
            case "Solicitud Aprobada":
              accionClase = "solicitud-aprobada";
              icono = '<i class="fa fa-check-circle"></i>';
              break;
            case "Solicitud Rechazada":
              accionClase = "solicitud-rechazada";
              icono = '<i class="fa fa-times-circle"></i>';
              break;
            default:
              accionClase = "";
              icono = '<i class="fa fa-question-circle"></i>';
              break;
          }

          detalles += `
            <li class="list-group-item ${accionClase}">
              <span class="icono">${icono}</span>
              <strong>Acción:</strong> ${seg.seg_accion} <br>
              <strong>Comentario:</strong> ${seg.seg_comentario} <br>
              <span class="text-muted"><i class="fa fa-calendar"></i> ${seg.seg_fecha}</span>
            </li>
          `;
        });
        detalles += "</ul>";

        seguimientoDiv.html(detalles).fadeIn();
      },
      error: function (xhr, status, error) {
        console.error("Error al obtener seguimiento:", error);
      },
    });
  }
});
