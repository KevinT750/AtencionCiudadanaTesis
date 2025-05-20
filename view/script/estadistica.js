function llenarSelect() {
  $.ajax({
    url: "../ajax/solicitud.php?op=obtAsunto",
    type: "GET",
    dataType: "json",
    success: function (data) {
      let select = $("#asunto");
      select.empty();
      select.append('<option value="">Seleccione el motivo</option>');
      data.forEach(function (asunto) {
        select.append(
          '<option value="' +
            asunto.asunto_id +
            '">' +
            asunto.asunto_nombre +
            "</option>"
        );
      });
    },
    error: function () {
      alert("Error al cargar los asuntos.");
    },
  });
}

function activarSelect() {
  var anio = document.getElementById("anio").value;
  var mes = document.getElementById("mes").value;

  if (anio === "") {
    document.getElementById("mes").disabled = true;
    document.getElementById("asunto").disabled = true;
    return;
  } else {
    document.getElementById("mes").disabled = false;
  }

  if (mes === "") {
    document.getElementById("asunto").disabled = true;
    return;
  } else {
    document.getElementById("asunto").disabled = false;
    // Si quieres llenar el select cuando se habilita:
    if ($("#asunto").children().length <= 1) {
      // si sólo tiene la opción por defecto
      llenarSelect();
    }
  }
}

function pieMostrar() {
  var today = new Date();
  var year = today.getFullYear();
  let anio = document.getElementById("anio").value || year;
  let mensaje = "Solicitudes por Mes del año " + anio + " (Gráfico de Pastel)";

  $.ajax({
    url: "../ajax/solicitud.php?op=obtDatosSolicitud",
    type: "POST",
    dataType: "json",
    data: { anio: anio },
    success: function (response) {
      if (!Array.isArray(response)) {
        console.error("Respuesta inesperada:", response);
        return;
      }

      let etiquetas = [];
      let valores = [];
      const meses = [
        "",
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre",
      ];

      response.forEach((item) => {
        etiquetas.push(meses[parseInt(item[0])]); // convierte número a nombre
        valores.push(item[1]);
      });

      const ctx = document.getElementById("pieChart").getContext("2d");
      // Destruir gráfico anterior si existe
      if (window.miPastel) {
        window.miPastel.destroy();
      }
      window.miPastel = new Chart(ctx, {
        type: "pie",
        data: {
          labels: etiquetas,
          datasets: [
            {
              label: "Solicitudes",
              data: valores,
              backgroundColor: [
                "#FF6384",
                "#36A2EB",
                "#FFCE56",
                "#8AFFC1",
                "#FFA07A",
                "#9370DB",
                "#00CED1",
                "#DC143C",
                "#00FA9A",
                "#FFD700",
                "#C71585",
                "#20B2AA",
              ],
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: "bottom",
            },
            title: {
              display: true,
              text: mensaje,
            },
          },
        },
      });
    },
    error: function (xhr, status, error) {
      console.error("Error en la petición AJAX:", error);
    },
  });
}
$(document).ready(function () {
  activarSelect(); // Inicializa el estado
  pieMostrar(); // Inicializa el gráfico
});

document.getElementById('anio').addEventListener('change', () => {
  activarSelect();
  pieMostrar();
});
