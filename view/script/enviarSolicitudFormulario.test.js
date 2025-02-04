/**
 * @jest-environment jsdom
 */
import { enviarSolicitudFormulario } from "../script/solicitud.js";

global.$ = require("jquery");
require("jest-fetch-mock").enableMocks();

describe("enviarSolicitudFormulario", () => {
  beforeEach(() => {
    document.body.innerHTML = `
      <form id="formSolicitud">
        <input type="file" id="archivo">
        <button id="submitBtn">Enviar</button>
      </form>
    `;
    fetch.resetMocks();
  });

  it("debería deshabilitar el botón al hacer clic", () => {
    enviarSolicitudFormulario("formSolicitud", "archivo", "submitBtn", "test-url");

    const btn = document.getElementById("submitBtn");
    btn.click();

    expect(btn.disabled).toBeTruthy();
  });

  it("debería enviar una solicitud AJAX al servidor", async () => {
    fetch.mockResponseOnce(JSON.stringify({ estado: true }));

    enviarSolicitudFormulario("formSolicitud", "archivo", "submitBtn", "test-url");

    const btn = document.getElementById("submitBtn");
    btn.click();

    expect(fetch).toHaveBeenCalledWith("test-url", expect.anything());
  });
});
