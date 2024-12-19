function cambiarTextoAsunto() {
    const comboBox = document.getElementById('asuntoCombo');
    const textArea = document.getElementById('asuntoTexto');
    const seleccion = comboBox.value;

    const textosPredefinidos = {
        "Cambio de Carreras": "Detalle el motivo por el cual solicita el cambio de carrera.",
        "Homologaciones": "Especifique las asignaturas que desea homologar y adjunte los documentos necesarios.",
        "Matrículas": "Indique el problema o solicitud relacionada con la matrícula.",
        "Otro": "" 
    };

    textArea.value = textosPredefinidos[seleccion] || "";
    textArea.readOnly = seleccion !== "Otro"; 
}