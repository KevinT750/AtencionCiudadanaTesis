function llenarSelect(){
  $.ajax({
        url: '../ajax/solicitud.php?op=obtAsunto',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            let select = $('#asunto');
            select.empty(); // Limpia opciones anteriores
            select.append('<option value="">Seleccione el motivo</option>');
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

$(document).ready(function () {
  llenarSelect();
});