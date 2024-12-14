$(document).ready(function() {
    $("#frmAcceso").on('submit', function(e) {
        e.preventDefault();
        
        let logina = $("#logina").val();
        let clavea = $("#clavea").val();
        
        if (!logina || !clavea) {
            bootbox.alert("Por favor, complete todos los campos");
            return;
        }
        
        let btnSubmit = $(this).find('button[type="submit"]');
        let originalText = btnSubmit.text();
        btnSubmit.prop('disabled', true).text('Procesando...');

        $.ajax({
            url: "../ajax/usuario.php?op=verificar",
            type: "POST",
            data: {
                "logina": logina,
                "clavea": clavea
            },
            success: function(response) {
                try {
                    let data = JSON.parse(response);
                    if (data.error) {
                        bootbox.alert(data.error);
                    } else if (data.success) {
                        window.location.href = "escritorio.php";  // Redirige si el login es exitoso
                    }
                } catch (e) {
                    bootbox.alert("Error en el servidor");
                }
            },
            error: function(xhr, status, error) {
                bootbox.alert("Error en la conexión: " + error);
            },
            complete: function() {
                btnSubmit.prop('disabled', false).text(originalText);
            }
        });
    });
});
