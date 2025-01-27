$(document).ready(function () {
    // Búsqueda por nombre
    $('#nombre').on('keyup', function () {
        let valor = $(this).val();
        if (valor.length > 0) {
            $.ajax({
                url: '../ajax/usuario.php?op=estudianteId',
                method: 'POST',
                data: {
                    tipo: 'nombre',
                    valor: valor,
                    accion: 'estudianteId'
                },
                success: function (respuesta) {
                    let resultados = JSON.parse(respuesta);
                    let html = '';
                    if (resultados.length > 0) {
                        resultados.forEach(function (resultado) {
                            html += `<li class="list-item" 
                                         data-id="${resultado.id}" 
                                         data-nombre="${resultado.nombre}" 
                                         data-cedula="${resultado.cedula}">
                                ${resultado.nombre} - ${resultado.cedula}
                            </li>`;
                        });
                    } else {
                        html = '<li>Sin resultados</li>';
                    }
                    $('#resultados-nombre').html(html).show();
                }
            });
        } else {
            $('#resultados-nombre').hide();
        }
    });

    // Búsqueda por cédula
    $('#cedula').on('keyup', function () {
        let valor = $(this).val();
        if (valor.length > 0) {
            $.ajax({
                url: '../ajax/usuario.php?op=estudianteId',
                method: 'POST',
                data: {
                    tipo: 'cedula',
                    valor: valor,
                    accion: 'estudianteId'
                },
                success: function (respuesta) {
                    let resultados = JSON.parse(respuesta);
                    let html = '';
                    if (resultados.length > 0) {
                        resultados.forEach(function (resultado) {
                            html += `<li class="list-item" 
                                         data-id="${resultado.id}" 
                                         data-nombre="${resultado.nombre}" 
                                         data-cedula="${resultado.cedula}">
                                ${resultado.cedula} - ${resultado.nombre}
                            </li>`;
                        });
                    } else {
                        html = '<li>Sin resultados</li>';
                    }
                    $('#resultados-cedula').html(html).show();
                }
            });
        } else {
            $('#resultados-cedula').hide();
        }
    });

    // Seleccionar un resultado de la lista
    $(document).on('click', '.list-item', function () {
        let id = $(this).data('id');         // Obtener la ID
        let nombre = $(this).data('nombre'); // Obtener el nombre
        let cedula = $(this).data('cedula'); // Obtener la cédula

        // Rellenar los campos de texto
        $('#nombre').val(nombre);
        $('#cedula').val(cedula);

        // Guardar la ID seleccionada en un atributo de datos o campo oculto
        $('#formSubirSolicitud').data('id', id);

        // Ocultar las listas de resultados
        $('.list-group').hide();
    });

    // Ocultar la lista si se hace clic fuera
    $(document).click(function (event) {
        if (!$(event.target).closest('.form-group').length) {
            $('.list-group').hide();
        }
    });

    /* Ejemplo de cómo usar la ID al enviar el formulario
    $('#formSubirSolicitud').on('submit', function (e) {
        e.preventDefault(); // Evitar el envío por defecto
        let id = $(this).data('id'); // Obtener la ID seleccionada
        let formData = new FormData(this); // Obtener los datos del formulario
        formData.append('id', id); // Agregar la ID al envío

        // Enviar el formulario con AJAX
        $.ajax({
            url: '../ajax/usuario.php?op=subirSolicitud',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (respuesta) {
                alert('Solicitud subida exitosamente.');
                location.reload();
            },
            error: function () {
                alert('Error al subir la solicitud.');
            }
        });
    });*/
});
