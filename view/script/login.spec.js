describe('Login', () => {
    let $fixture;

    beforeEach(() => {
        // Crear el contenedor para renderizar el formulario
        $fixture = $('<div id="loginContainer"><form id="frmAcceso"><input type="text" id="logina" value="testuser"><input type="password" id="clavea" value="password"><button type="submit">Ingresar</button></form></div>');
        $('body').append($fixture); // Agregar al cuerpo temporalmente

        // Espiar el método AJAX
        spyOn($, 'ajax').and.callFake(function(options) {
            options.success({ success: true }); // Simular respuesta exitosa
        });

        // Espiar la redirección de la página
        spyOn(window, 'location', 'set'); // Esto evita que la página se recargue
    });

    afterEach(() => {
        $fixture.remove(); // Limpiar después de cada prueba
    });

    it('debería enviar el formulario de login correctamente', () => {
        const form = $('#frmAcceso');
        form.submit(); // Simular envío del formulario

        expect($.ajax).toHaveBeenCalled(); // Verificar si se ha llamado AJAX
        expect($.ajax.calls.mostRecent().args[0].data.logina).toBe('testuser'); // Verificar que se envíen los datos correctos
        expect($.ajax.calls.mostRecent().args[0].data.clavea).toBe('password');
    });

    it('debería redirigir si la autenticación es exitosa', () => {
        const form = $('#frmAcceso');
        form.submit(); // Simular envío del formulario

        // Verificar si no se recarga la página durante las pruebas
        expect(window.location.href).toBe('escritorio.php'); // Verificar si se redirige a la página correcta
    });

    it('debería mostrar un mensaje de error si la autenticación falla', () => {
        // Modificar la respuesta para simular error
        $.ajax.and.callFake(function(options) {
            options.success({ error: 'Credenciales incorrectas' });
        });

        const form = $('#frmAcceso');
        spyOn(bootbox, 'alert'); // Espiar la alerta de bootbox

        form.submit(); // Simular envío del formulario

        expect(bootbox.alert).toHaveBeenCalledWith('Credenciales incorrectas'); // Verificar si se muestra el mensaje de error
    });
});
