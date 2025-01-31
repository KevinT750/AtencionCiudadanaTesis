$(document).ready(function() {
    $('#btnSubmit').on('click', function(e) {
        e.preventDefault();
        
        var usuario = $('#logina').val();
        var clave = $('#clavea').val();
        var $btnSubmit = $('#btnSubmit');
        
        // Validación de campos vacíos
        if (usuario === '' || clave === '') {
            alert('Por favor, complete todos los campos');
            return;
        }

        // Bloquear el botón antes de iniciar el proceso
        $btnSubmit.prop('disabled', true);
        $btnSubmit.text('Procesando...');

        // Simulamos un proceso de login (pero no lo desbloqueamos automáticamente)
        setTimeout(function() {
            // Simular verificación de credenciales
            if (usuario === 'admin' && clave === '1234') {
                alert('Ingreso exitoso');
                // Solo desbloqueamos el botón en caso de éxito
                $btnSubmit.prop('disabled', false).text('Enviar');
            } else {
                alert('Credenciales incorrectas');
            }
        }, 1000);
    });
});
