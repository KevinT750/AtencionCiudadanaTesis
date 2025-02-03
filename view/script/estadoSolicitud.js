$(document).ready(async function (params) {
  $("#solicitudesTable").DataTable({
    ajax: {
      url: "../ajax/usuario.php?op=estadoSolicitud",
      type: "GET",
      data: { tipo: 0 },
      dataSrc: function (json) {
        if (json.aaData) {
          return json.aaData;
        } else {
          console.error(
            "Error: No se encontró 'aaData' en la respuesta JSON.",
            json
          );
          return [];
        }
      },
      error: function (xhr, error, thrown) {
        console.error(
          "Error al cargar datos en DataTable:",
          xhr,
          error,
          thrown
        );
      },
    },
    columns: [
      {
        data: 0, // Fecha
        title: "Fecha", // Título de la columna
      },
      {
        data: 1, // ID de solicitud
        render: function (data, type, row) {
          return `
                    <button 
                      class="btn btn-info btn-sm ver-solicitud" 
                      data-file-url="https://drive.google.com/file/d/${data}/preview" 
                      title="Ver Solicitud">
                      <i class="fa fa-eye"></i> Ver Solicitud
                    </button>`;
        },
        title: "Acción", // Título de la columna
      },
      {
        data: 2, // Documento
        render: function (data, type, row) {
          return `
                    <button 
                      class="btn btn-success btn-sm ver-otro-dato" 
                      data-file-url="https://drive.google.com/file/d/${data}/preview" 
                      title="Ver Documento">
                      <i class="fa fa-eye"></i> Ver Documento
                    </button>`;
        },
        title: "Acción", // Título de la columna
      },
      {
        data: 3, // Estado (Leído o No Leído)
        render: function (data, type, row) {
          let badgeClass, titleText;

          // Si el estado es "No Leído", lo mostramos como "Enviado"
          if (data === "No Leído") {
            data = "Enviado"; // Cambiar "No Leído" a "Enviado"
          }

          switch (data) {
            case "Enviado":
              badgeClass = "primary";
              titleText = "Solicitud Enviada";
              break;
            case "Leído":
              badgeClass = "success";
              titleText = "Solicitud Leída";
              break;
            case "Aceptado":
              badgeClass = "info";
              titleText = "Solicitud Aceptada";
              break;
            case "Rechazado":
              badgeClass = "danger";
              titleText = "Solicitud Rechazada";
              break;
            default:
              badgeClass = "secondary";
              titleText = "Estado Desconocido";
              break;
          }

          // Devuelve el badge con el texto y estilo correspondientes
          return `<span class="badge bg-${badgeClass}" title="${titleText}">${data}</span>`;
        },
        title: "Estado", // Título de la columna
      },

      {
        data: null, // Botón para cancelar
        render: function (data, type, row) {
          var disableButton =
            row[3] === "Aceptado" || row[3] === "Rechazado" ? "disabled" : "";
          return `
                    <button 
                      class="btn btn-danger btn-sm cancelar-datos" 
                      data-columna-2="${row[1]}" 
                      data-columna-3="${row[2]}" ${disableButton} 
                      title="Cancelar">
                      <i class="fa fa-trash"></i> Cancelar
                    </button>`;
        },
        title: "Acción", // Título de la columna
      },
    ],
    responsive: true, // Hacer la tabla responsiva
    paging: true, // Habilitar paginación
    lengthChange: false, // Deshabilitar la opción de cambiar el número de filas por página
    info: true, // Mostrar información de la tabla
    autoWidth: false, // Desactivar ajuste automático de ancho de las columnas
  });

  // Evento para abrir el archivo en una nueva ventana
  $("#solicitudesTable").on("click", ".ver-solicitud", function () {
    var fileUrl = $(this).data("file-url"); // Obtener la URL del archivo
    console.log("Ver archivo con URL:", fileUrl);
    modalSol(fileUrl);
  });

  // Evento para abrir otro archivo en una nueva ventana
  $("#solicitudesTable").on("click", ".ver-otro-dato", function () {
    var fileUrl = $(this).data("file-url"); // Obtener la URL del archivo
    console.log("Ver archivo con URL:", fileUrl);
    modalSol(fileUrl);
  });

  function modalSol(idDrive) {
    const data = {
      id: idDrive,
    };

    $.ajax({
      url: "../ajax/solicitud.php?op=modalSol", // Ruta al archivo PHP
      type: "POST",
      dataType: "json", // Asegúrate de que la respuesta se maneje como JSON
      data: data,
      success: function (response) {
        // Verifica si la respuesta contiene el contenido necesario
        if (response && response.modalContent) {
          // Inserta el contenido del modal en el body
          const modalContainer = $("<div>").html(response.modalContent);
          $("body").append(modalContainer);

          // Asegúrate de que el modal y el overlay tengan la clase show para hacerlos visibles
          $("#documentModal").addClass("show");
          $("#overlay").addClass("show");

          // Opcional: Añadir un listener para cerrar el modal cuando el overlay sea clickeado
          $("#overlay").on("click", function () {
            $("#documentModal").remove(); // Eliminar modal
            $("#overlay").remove(); // Eliminar overlay
          });

          // Añadir funcionalidad de cerrar el modal con el botón de cerrar
          $(".modal-close").on("click", function () {
            $("#documentModal").remove(); // Eliminar modal
            $("#overlay").remove(); // Eliminar overlay
          });
        } else {
          Swal.fire(
            "Error",
            "La respuesta del servidor no contiene el contenido esperado.",
            "error"
          );
        }
      },
      error: function () {
        Swal.fire(
          "Error",
          "No se pudo cargar el modal. Intenta de nuevo.",
          "error"
        );
      },
    });
  }

  $("#solicitudesTable").on("click", ".cancelar-datos", function () {
    // Obtener los datos del botón
    const sol_solicitud = $(this).data("columna-2");
    const sol_documento = $(this).data("columna-3");

    // Mostrar la alerta de confirmación con SweetAlert2
    Swal.fire({
      title: "Advertencia",
      text: "¿Estás seguro de que deseas eliminar esta solicitud y los archivos relacionados?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Aceptar",
      cancelButtonText: "Cancelar",
      reverseButtons: true,
    }).then((result) => {
      // Si el usuario confirma
      if (result.isConfirmed) {
        // Realizar la solicitud AJAX para eliminar los datos de la base de datos
        $.ajax({
          url: "../ajax/solicitud.php?op=Eliminar",
          type: "POST",
          data: {
            sol_solicitud: sol_solicitud,
            sol_documento: sol_documento,
          },
          success: function (response) {
            console.log("Datos eliminados de la base de datos:", response);

            // Proceder a eliminar el primer archivo de Google Drive
            $.ajax({
              url: "../ajax/drive.php?op=ElimiarArchivos",
              type: "POST",
              data: {
                archivoId: sol_solicitud, // ID del primer archivo
              },
              success: function (responseDrive1) {
                console.log(
                  "Primer archivo eliminado de Google Drive:",
                  responseDrive1
                );

                // Proceder a eliminar el segundo archivo de Google Drive
                $.ajax({
                  url: "../ajax/drive.php?op=ElimiarArchivos",
                  type: "POST",
                  data: {
                    archivoId: sol_documento, // ID del segundo archivo
                  },
                  success: function (responseDrive2) {
                    console.log(
                      "Segundo archivo eliminado de Google Drive:",
                      responseDrive2
                    );

                    // Mostrar mensaje de éxito y actualizar la tabla
                    Swal.fire({
                      title: "Éxito",
                      text: "La solicitud y los archivos se eliminaron exitosamente.",
                      icon: "success",
                      confirmButtonText: "Aceptar",
                    }).then(() => {
                      $("#solicitudesTable").DataTable().ajax.reload();
                    });
                  },
                  error: function (errorDrive2) {
                    console.error(
                      "Error al eliminar el segundo archivo en Google Drive:",
                      errorDrive2
                    );
                    Swal.fire({
                      title: "Error",
                      text: "Ocurrió un problema al eliminar el segundo archivo en Google Drive.",
                      icon: "error",
                      confirmButtonText: "Aceptar",
                    });
                  },
                });
              },
              error: function (errorDrive1) {
                console.error(
                  "Error al eliminar el primer archivo en Google Drive:",
                  errorDrive1
                );
                Swal.fire({
                  title: "Error",
                  text: "Ocurrió un problema al eliminar el primer archivo en Google Drive.",
                  icon: "error",
                  confirmButtonText: "Aceptar",
                });
              },
            });
          },
          error: function (errorDB) {
            console.error(
              "Error al eliminar datos en la base de datos:",
              errorDB
            );
            Swal.fire({
              title: "Error",
              text: "Ocurrió un problema al eliminar los datos de la base de datos.",
              icon: "error",
              confirmButtonText: "Aceptar",
            });
          },
        });
      }
    });
  });
});
