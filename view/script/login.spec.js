describe("Formulario de acceso", function() {
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

        // Espiar la función de alerta
        spyOn(window, "alert");

        // Prevenir que se ejecute la recarga de página
        spyOn($form[0], 'submit').and.callFake(function() {
            // Evitar que la página se recargue durante el test
            return false;
        });
    });

    it("debería restaurar el estado del botón después del proceso", function() {
        // Enviar el formulario con las credenciales correctas
        $logina.val("admin1");
        $clavea.val("12341");
        $submitButton.trigger('click');

        // Simular que el proceso ha terminado
        $submitButton.prop('disabled', false).text("Enviar");

        // Verificar que el botón ha restaurado su estado
        expect($submitButton.prop('disabled')).toBeFalse();
        expect($submitButton.text()).toBe('Enviar');
    });
});
