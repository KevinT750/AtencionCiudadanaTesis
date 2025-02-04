<?php
class Usuario {
    public function descargarSolicitud(string $rutaArchivo): bool {
        if (!file_exists($rutaArchivo)) {
            return false; // Simula un error si el archivo no existe
        }
        return true; // Simula una descarga exitosa
    }
}
