<?php

class EmailsCrud {

    // Ruta al archivo JSON
    private $filePath;

    public function __construct($filePath) {
        $this->filePath = $filePath;
    }

    // Leer los correos desde el archivo JSON
    public function getEmails() {
        if (file_exists($this->filePath)) {
            $emails = json_decode(file_get_contents($this->filePath), true);
            return $emails;
        } else {
            return [];
        }
    }

    // Agregar un nuevo correo al archivo JSON
    public function addEmail($correo) {
        $emails = $this->getEmails();
        array_push($emails, $correo);  // Agregar nuevo correo al final del array
        return $this->saveEmails($emails);  // Guardar los correos actualizados
    }

    // Editar un correo en el archivo JSON
    public function editEmail($index, $correo) {
        $emails = $this->getEmails();
        if (isset($emails[$index])) {
            $emails[$index] = $correo;  // Reemplazar el correo existente
            return $this->saveEmails($emails);  // Guardar los correos actualizados
        }
        return false;  // Si no se encuentra el correo
    }

    // Eliminar un correo del archivo JSON
    public function deleteEmail($index) {
        $emails = $this->getEmails();
        if (isset($emails[$index])) {
            unset($emails[$index]);  // Eliminar correo
            $emails = array_values($emails);  // Reindexar el array
            return $this->saveEmails($emails);  // Guardar los correos actualizados
        }
        return false;  // Si no se encuentra el correo
    }

    // Guardar los correos actualizados en el archivo JSON
    private function saveEmails($emails) {
        return file_put_contents($this->filePath, json_encode($emails, JSON_PRETTY_PRINT));
    }
}
?>
