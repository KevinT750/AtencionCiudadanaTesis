$(document).ready(function () {
  var table = $("#solicitudesTable").DataTable({
    ajax: {
      url: "../ajax/usuario.php?op=estadoSolicitud",
      type: "GET",
      data: { tipo: 0 }, // Cargar todas las solicitudes inicialmente
      dataSrc: function (json) {
        if (json.aaData) {
          // Filtrar las solicitudes que no tengan el estado "Documentos subidos"
          let filteredData = json.aaData.filter(
            (row) => row[3] !== "Documentos subidos"
          );
          return filteredData;
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
        title: "Fecha",
      },
      {
        data: 1, // ID de solicitud
        render: function (data, type, row) {
          return `
                      <button class="btn btn-info btn-sm ver-solicitud" data-file-url="https://drive.google.com/file/d/${data}/preview" title="Ver Solicitud">
                          <i class="fa fa-eye"></i> Ver Solicitud
                      </button>`;
        },
        title: "Acción",
      },
      {
        data: 2, // Documento
        render: function (data, type, row) {
          return `
                      <button class="btn btn-success btn-sm ver-otro-dato" data-file-url="https://drive.google.com/file/d/${data}/preview" title="Ver Documento">
                          <i class="fa fa-eye"></i> Ver Documento
                      </button>`;
        },
        title: "Acción",
      },
      {
        data: 3, // Estado
        render: function (data, type, row) {
          let className = "";
          let titleText = "";
          if (data === "No Leído") data = "Enviado";
          switch (data) {
            case "Enviado":
              className = "badge bg-primary";
              titleText = "Solicitud Enviada";
              break;
            case "Leído":
              className = "badge bg-info";
              titleText = "Solicitud Leída";
              break;
            case "Aceptado":
              className = "badge bg-success";
              titleText = "Solicitud Aceptada";
              break;
            case "Rechazado":
              className = "badge bg-danger";
              titleText = "Solicitud Rechazada";
              break;
            default:
              className = "badge bg-secondary";
              titleText = data;
              break;
          }
          return `<span class="${className}" title="${titleText}">${data}</span>`;
        },
        createdCell: function (td, cellData, rowData, row, col) {
          // Opcional: puedes agregar clases a la fila según el estado aquí si lo deseas
          $(td)
            .closest("tr")
            .removeClass(
              "estado-noleido estado-leido estado-aprobado estado-rechazado"
            );
          switch (cellData) {
            case "No Leído":
              $(td).closest("tr").addClass("estado-noleido");
              break;
            case "Leído":
              $(td).closest("tr").addClass("estado-leido");
              break;
            case "Aceptado":
              $(td).closest("tr").addClass("estado-aprobado");
              break;
            case "Rechazado":
              $(td).closest("tr").addClass("estado-rechazado");
              break;
          }
        },
        title: "Estado",
      },
      {
        data: null, // Botón para cancelar
        render: function (data, type, row) {
          var disableButton =
            row[3] === "Aceptado" || row[3] === "Rechazado" ? "disabled" : "";
          return `
                      <button class="btn btn-danger btn-sm cancelar-datos" data-columna-2="${row[1]}" data-columna-3="${row[2]}" ${disableButton} title="Cancelar">
                          <i class="fa fa-trash"></i> Cancelar
                      </button>`;
        },
        title: "Acción",
      },
    ],
    dom: "Bfrtip", // Agrega la barra de botones en la parte superior
    language: {
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ registros por página",
      zeroRecords: "No se encontraron resultados",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoEmpty: "No hay registros disponibles",
      infoFiltered: "(filtrado de _MAX_ registros en total)",
    },
    pagingType: "simple_numbers",
    ordering: true,
    searching: true,
    responsive: true,
    autoWidth: false,
    buttons: [
      {
        text: '<i class="fa fa-envelope"></i> Enviado',
        className: "btn btn-outline-primary m-1 rounded-pill shadow-sm",
        action: function () {
          table.column(3).search("^Enviado$", true, false).draw();
        },
        init: function (api, node, config) {
          $(node).attr("id", "btn-no-leido");
        },
      },
      {
        text: '<i class="fa fa-check-circle"></i> Leído',
        className: "btn btn-outline-success m-1 rounded-pill shadow-sm",
        action: function () {
          table.column(3).search("^Leído$", true, false).draw();
        },
        init: function (api, node, config) {
          $(node).attr("id", "btn-leido");
        },
      },
      {
        text: '<i class="fa fa-thumbs-up"></i> Aceptado',
        className: "btn btn-outline-info m-1 rounded-pill shadow-sm",
        action: function () {
          table.column(3).search("Aceptado").draw();
        },
        init: function (api, node, config) {
          $(node).attr("id", "btn-aceptado");
        },
      },
      {
        text: '<i class="fa fa-times-circle"></i> Rechazado',
        className: "btn btn-outline-danger m-1 rounded-pill shadow-sm",
        action: function () {
          table.column(3).search("Rechazado").draw();
        },
        init: function (api, node, config) {
          $(node).attr("id", "btn-rechazado");
        },
      },
      {
        text: '<i class="fa fa-list"></i> Todos',
        className: "btn btn-outline-secondary m-1 rounded-pill shadow-sm",
        action: function () {
          table.column(3).search("").draw();
        },
        init: function (api, node, config) {
          $(node).attr("id", "btn-todos");
        },
      },
    ],
    responsive: true,
    paging: true,
    lengthChange: false,
    info: true,
    autoWidth: false,
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
