$(document).ready(function () {
  obtenerSolicitudes();

  function obtenerSolicitudes() {
    $.ajax({
      url: "../ajax/solicitud.php?op=obSolSeg",
      type: "GET",
      dataType: "json",
      success: function (data) {
        if (data.error) {
          alert(data.error);
          return;
        }
        mostrarSolicitudes(data);
      },
      error: function (xhr, status, error) {
        console.error("Error al obtener solicitudes:", error);
      },
    });
  }

  function mostrarSolicitudes(solicitudes) {
    let lista = $("#listaSolicitudes");
    lista.empty();

    solicitudes.forEach(solicitud => {
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
                    <button class="btn btn-primary btn-ver px-4 py-2">
                        <i class="fa fa-eye"></i> Ver Seguimiento
                    </button>
                    <div class="seguimiento mt-3 p-3 bg-light rounded shadow-sm" style="display: none;"></div>
                </div>
            </div>
        `);

        // Evento para ver el seguimiento
        item.find(".btn-ver").click(function () {
            let seguimientoDiv = item.find(".seguimiento");

            if (seguimientoDiv.is(":empty")) {
                obtenerSeguimiento(solicitud.sol_id, seguimientoDiv);
            } else {
                seguimientoDiv.slideToggle(); // Animación suave
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
          let color = "";

          // Asignamos clases y iconos según el tipo de acción
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
