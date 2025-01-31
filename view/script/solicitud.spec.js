describe('Solicitudes Secretarias - Tests', function () {
  let $table;

  beforeEach(function () {
      // Cargar el HTML necesario para la prueba
      document.body.innerHTML = `
          <table id="solicitudesSecret"></table>
      `;
      
      // Espiar funciones jQuery
      spyOn($, 'ajax').and.callThrough(); // Espiar la llamada AJAX

      // Inicializar DataTable
      $table = $('#solicitudesSecret');
      spyOn($.fn, 'DataTable').and.callFake(function () {
          return {
              ajax: { url: '../ajax/solicitud.php?op=Solicitudes', type: 'GET' },
              columns: [],
          };
      });

      // Establecer un entorno básico para las pruebas (por ejemplo, simular datos)
      $.ajax.calls.mostRecent().returnValue = {
          aaData: [
              [1, 'Solicitud 1', 'Descripción', 'No Leído', '1', 'document1', 'file1', 'Leído'],
              [2, 'Solicitud 2', 'Descripción', 'Leído', '2', 'document2', 'file2', 'No Leído']
          ]
      };
  });

  it('debería inicializar DataTable correctamente', function () {
      // Inicializamos DataTable
      $('#solicitudesSecret').DataTable();
      
      // Verificar que DataTable fue llamado correctamente
      expect($.fn.DataTable).toHaveBeenCalled();
  });

  it('debería realizar una llamada AJAX al cargar los datos', function () {
      // Disparar el evento de carga de datos
      $('#solicitudesSecret').DataTable().ajax.reload();

      // Verificar que la función AJAX haya sido llamada
      expect($.ajax).toHaveBeenCalled();
      expect($.ajax).toHaveBeenCalledWith(jasmine.objectContaining({
          url: '../ajax/solicitud.php?op=Solicitudes',
          type: 'GET'
      }));
  });

  it('debería abrir la URL correcta cuando se hace clic en el botón "Ver Solicitud"', function () {
      spyOn(window, 'open'); // Espiar la función window.open

      // Simular clic en el botón
      $('#solicitudesSecret').trigger('click');

      // Verificar que la URL correcta fue llamada
      expect(window.open).toHaveBeenCalledWith('https://docs.google.com/document/d/document1/view', '_blank');
  });

  it('debería mostrar mensaje al hacer clic en "Dejar Comentario"', function () {
      // Simular clic en el botón "Dejar Comentario"
      const $btnCancelar = $('<button>').addClass('cancelar-datos').data('columna-2', 'data2').data('columna-3', 'data3');
      $('body').append($btnCancelar);
      
      spyOn($, 'ajax').and.callFake(function (options) {
          // Simular la respuesta del AJAX
          options.success('success');
      });

      $btnCancelar.trigger('click');

      // Verificar si el modal ha sido agregado
      expect($('body').find('#modalSubir').length).toBeGreaterThan(0);
  });

  it('debería mostrar mensaje de error si no se proporciona un mensaje', function () {
      spyOn(window, 'Swal'); // Espiar la función Swal

      // Simular el clic en el botón de enviar mensaje
      $('#btnEnviar').trigger('click');

      // Verificar que se muestra un mensaje de error
      expect(window.Swal).toHaveBeenCalledWith(jasmine.objectContaining({
          icon: 'error',
          title: 'Error',
          text: 'Por favor, escribe un mensaje antes de enviar.'
      }));
  });
});
