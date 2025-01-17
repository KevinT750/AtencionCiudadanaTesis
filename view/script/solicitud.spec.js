const sinon = require('sinon');
const { expect } = require('chai');
const $ = require('jquery'); 

describe('Función enviarSolicitudFormulario', function () {
    let sandbox;
  
    beforeEach(function () {
      // Crear un contenedor temporal para el DOM
      sandbox = document.createElement('div');
      sandbox.id = 'sandbox';
      document.body.appendChild(sandbox);
  
      // Crear un formulario de prueba y los elementos necesarios
      const formHtml = `
        <form id="formSolicitud">
          <input type="file" id="archivo">
          <button type="button" id="submitBtn">Enviar</button>
        </form>
      `;
      sandbox.innerHTML = formHtml;
    });
  
    afterEach(function () {
      // Limpiar el DOM después de cada prueba
      sandbox.remove();
    });
  
    it('Debería realizar una solicitud AJAX cuando el botón de enviar sea presionado', function (done) {
      // Preparar el stub de la función AJAX
      const fakeResponse = { estado: true };
      const fakeSuccess = sinon.stub($, 'ajax').yieldsTo('success', JSON.stringify(fakeResponse));
  
      // Simular el clic en el botón
      const submitButton = document.getElementById('submitBtn');
      submitButton.click();
  
      // Esperar a que la solicitud AJAX termine
      setTimeout(function () {
        // Comprobar si la solicitud fue realizada
        expect(fakeSuccess.calledOnce).to.be.true;
  
        // Restaurar el stub
        fakeSuccess.restore();
  
        done();
      }, 100);
    });
  
    it('Debería mostrar un mensaje de éxito cuando la respuesta del servidor sea correcta', function (done) {
      // Preparar el stub de Swal
      const swalStub = sinon.stub(window, 'Swal').returns(Promise.resolve());
  
      // Simular la función de éxito
      const fakeResponse = { success: true };
      const fakeSuccess = sinon.stub($, 'ajax').yieldsTo('success', fakeResponse);
  
      // Simular el clic en el botón
      const submitButton = document.getElementById('submitBtn');
      submitButton.click();
  
      // Esperar a que la solicitud termine
      setTimeout(function () {
        // Comprobar que se llamó a Swal con el mensaje de éxito
        expect(swalStub.calledWith(sinon.match({ title: 'Éxito' }))).to.be.true;
  
        // Restaurar los stubs
        fakeSuccess.restore();
        swalStub.restore();
  
        done();
      }, 100);
    });
  
    it('Debería mostrar un mensaje de error cuando la respuesta del servidor sea incorrecta', function (done) {
      // Preparar el stub de Swal
      const swalStub = sinon.stub(window, 'Swal').returns(Promise.resolve());
  
      // Simular la función de error
      const fakeResponse = { success: false, error: 'Error del servidor' };
      const fakeError = sinon.stub($, 'ajax').yieldsTo('error', fakeResponse);
  
      // Simular el clic en el botón
      const submitButton = document.getElementById('submitBtn');
      submitButton.click();
  
      // Esperar a que la solicitud termine
      setTimeout(function () {
        // Comprobar que se llamó a Swal con el mensaje de error
        expect(swalStub.calledWith(sinon.match({ title: 'Error' }))).to.be.true;
  
        // Restaurar los stubs
        fakeError.restore();
        swalStub.restore();
  
        done();
      }, 100);
    });
  });
  