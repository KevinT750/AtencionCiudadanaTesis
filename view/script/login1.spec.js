describe('Login Form', function () {
    let $form, $logina, $clavea, $submitButton;

    beforeEach(function() {
        // Cargar el HTML necesario para la prueba
        document.body.innerHTML = `
            <form id="frmAcceso">
                <input type="text" id="logina" value="" />
                <input type="password" id="clavea" value="" />
                <button type="button" id="btnSubmit">Enviar</button>
            </form>
        `;

        // Obtener los elementos
        $form = $("#frmAcceso");
        $logina = $("#logina");
        $clavea = $("#clavea");
        $submitButton = $("#btnSubmit");

        

        // Prevenir que se ejecute la recarga de página
        spyOn($form[0], 'submit').and.callFake(function() {
            // Evitar que la página se recargue durante el test
            return false;
        });
    });

    it('debería mostrar alerta si los campos están vacíos', function () {
        spyOn(window, 'alert');
        $('#logina').val('');
        $('#clavea').val('');
        $('#btnSubmit').trigger('click');
        expect(window.alert).toHaveBeenCalledWith('Por favor, complete todos los campos');
    });
    

    it('debería mostrar alerta de éxito con credenciales correctas', function (done) {
        spyOn(window, 'alert');
        $('#logina').val('admin');
        $('#clavea').val('1234');
        $('#btnSubmit').trigger('click');
    
        setTimeout(function () {
            expect(window.alert).toHaveBeenCalledWith('Ingreso exitoso');
            done();
        }, 100);
    });
    

    it('debería mostrar alerta de error con credenciales incorrectas', function (done) {
        spyOn(window, 'alert');
        $('#logina').val('usuario');
        $('#clavea').val('wrongpass');
        $('#btnSubmit').trigger('click');

        setTimeout(function () {
            expect(window.alert).toHaveBeenCalledWith('Credenciales incorrectas');
            done();
        }, 1100);
    });
});
