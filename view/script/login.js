$(document).ready(function() {
    $('#btnSubmit').on('click', function(e) {
        e.preventDefault();
        
        var usuario = $('#logina').val();
        var clave = $('#clavea').val();
        
        // Validación de campos vacíos
        if (usuario === '' || clave === '') {
            alert('Por favor, complete todos los campos');
            return;
        }
        
        // Cambiar el texto del botón y deshabilitarlo durante el proceso
        var $btnSubmit = $('#btnSubmit');
        $btnSubmit.prop('disabled', true);
        $btnSubmit.text('Procesando...');
        
        // Simulamos un login
        setTimeout(function() {
            // Simulamos la verificación de credenciales
            if (usuario === 'admin' && clave === '1234') {
                alert('Ingreso exitoso');
            } else {
                alert('Credenciales incorrectas');
            }
            
            // Restauramos el botón después del proceso
            $btnSubmit.prop('disabled', false);
            $btnSubmit.text('Enviar');
        }, 1000);
    });
});
