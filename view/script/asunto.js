function cambiarTextoAsunto() {
    const comboBox = document.getElementById('asuntoCombo');
    const textArea = document.getElementById('asuntoTexto');
    const seleccion = comboBox.value;

    const textosPredefinidos = {
        "Cambio de Carreras": "Detalle el motivo por el cual solicita el cambio de carrera.",
        "Homologaciones": "Especifique las asignaturas que desea homologar y adjunte los documentos necesarios.",
        "Matrículas": "Indique el problema o solicitud relacionada con la matrícula.",
        "Certificados": "Describa el certificado que necesita y su finalidad.",
        "Título": "Explique el motivo de su solicitud de título.",
        "Otro": "" // Dejar vacío para permitir texto personalizado
    };

    textArea.value = textosPredefinidos[seleccion] || "";
    textArea.readOnly = seleccion !== "Otro"; 
}