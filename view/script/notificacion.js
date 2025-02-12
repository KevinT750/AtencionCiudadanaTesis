$(document).ready(function () {
    // Función para cargar las notificaciones
    function cargarNotificaciones() {
      $.ajax({
        url: "../ajax/solicitud.php?op=obtenerNotificaciones",
        type: "GET",
        dataType: "json",
        success: function (response) {
          if (response.error) {
            console.error(response.error);
            return;
          }
  
          let notificaciones = response.notificaciones;
          let contadorNoVistas = response.contadorNoVistas; // Contador de las notificaciones no vistas
          $("#notificacion-count").text(contadorNoVistas); // Actualizar el contador de notificaciones no vistas
          $("#notificacion-text").text(contadorNoVistas); // Actualizar el texto del contador
          $("#notificacion-list").empty(); // Limpiar la lista de notificaciones
  
          if (notificaciones.length > 0) {
            let nuevas = [];
            let hoy = [];
            let ayer = [];
            let anteriores = [];
  
            // Función para formatear la fecha
            function formatFecha(fecha) {
              const options = {
                year: "numeric",
                month: "numeric",
                day: "numeric",
              };
              return new Date(fecha).toLocaleDateString(undefined, options);
            }
  
            notificaciones.forEach((notif) => {
              const fechaNotificacion = new Date(notif.fecha);
              const hoyFecha = new Date();
              const ayerFecha = new Date();
              ayerFecha.setDate(hoyFecha.getDate() - 1);
  
              // Definir el texto de la notificación según su fecha
              if (notif.visto == 0) {
                nuevas.push(notif);
              } else if (
                formatFecha(fechaNotificacion) === formatFecha(hoyFecha)
              ) {
                hoy.push(notif);
              } else if (
                formatFecha(fechaNotificacion) === formatFecha(ayerFecha)
              ) {
                ayer.push(notif);
              } else {
                anteriores.push(notif);
              }
            });
  
            // Mostrar notificaciones por categorías
            const categorias = [
              { nombre: "Nuevas", notificaciones: nuevas },
              { nombre: "Hoy", notificaciones: hoy },
              { nombre: "Ayer", notificaciones: ayer },
              { nombre: "Anteriores", notificaciones: anteriores },
            ];
  
            categorias.forEach((categoria) => {
              if (categoria.notificaciones.length > 0) {
                $("#notificacion-list").append(
                  `<li class="header">${categoria.nombre}</li>`
                );
                categoria.notificaciones.slice(0, 10).forEach((notif) => {
                  let icon = "fa-info-circle text-blue";
                  if (notif.accion.toLowerCase().includes("rechazada")) {
                    icon = "fa-times-circle text-red";
                  } else if (notif.accion.toLowerCase().includes("aprobada")) {
                    icon = "fa-check-circle text-green";
                  }
  
                  $("#notificacion-list").append(`
                                      <li style="background-color: white; padding: 10px; border-radius: 5px; margin-bottom: 5px;">
                                          <a href="#" class="notificacion-link" data-seg-id="${notif.id}" data-sol-id="${notif.solicitud}" style="display: block; text-decoration: none; color: black;">
                                              <i class="fa ${icon}"></i> ${notif.accion}
                                          </a>
                                      </li>
                                  `);
                });
              }
            });
  
            if (notificaciones.length > 10) {
              $("#notificacion-list").append(
                '<li class="footer"><a href="notificaciones.php">Mostrar todas</a></li>'
              );
            }
          } else {
            $("#notificacion-list").append(
              `<li><a href="#">No tienes notificaciones</a></li>`
            );
          }
        },
        error: function (xhr, status, error) {
          console.error("Error al obtener notificaciones:", error);
        },
      });
    }
  
    // Función para marcar la notificación como vista
    function marcarNotificacionVisto(seg_id, sol_id) {
      $.ajax({
          url: "../ajax/solicitud.php?op=cambiarVis",
          type: "POST",
          data: { seg_id: seg_id },
          dataType: "json",
          success: function (response) {
              if (response.success) {
                  console.log("Notificación marcada como vista.");
                  cargarNotificaciones(); // Recargar lista tras marcar como vista
               
                  if (sol_id) {
                      // Redirigir a la página de seguimiento usando solo 'sol_id'
                      window.location.href = `seguimientoID.php?sol_id=${sol_id}`;
                  } else {
                      console.error("No se encontró 'sol_id' en la respuesta.");
                  }
              } else {
                  console.error("Error al marcar la notificación como vista.");
              }
          },
          error: function (xhr, status, error) {
              console.error("Error al marcar la notificación como vista:", error);
          }
      });
    }
  
    // Evento cuando se hace clic en una notificación
    $(document).on("click", ".notificacion-link", function (e) {
      e.preventDefault();
      const seg_id = $(this).data("seg-id");
      const sol_id = $(this).data("sol-id"); // Obtener 'sol_id' desde el atributo
      marcarNotificacionVisto(seg_id, sol_id); // Pasar ambos parámetros
    });
  
    // Inicializar
    cargarNotificaciones();
    setInterval(cargarNotificaciones, 60000); // Recargar cada 60 segundos
  });
  