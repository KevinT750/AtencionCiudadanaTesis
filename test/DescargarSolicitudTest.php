<?php
use PHPUnit\Framework\TestCase;

class DescargarSolicitudTest extends TestCase {

    public function testGetFileForDownload() {
        // Ruta absoluta del archivo
        $filePath = realpath(__DIR__ . '/../public/document/Solicitud Estudiantes 2024_Vigente.docx');
        
        // Verificar si el archivo existe
        if (!$filePath) {
            $this->fail('El archivo no existe en la ruta proporcionada.');
        }

        // Comprobar si el archivo se puede acceder y si existe
        $this->assertFileExists($filePath, 'El archivo no existe en la ruta especificada');

        // Obtener el tamaño del archivo
        $fileSize = filesize($filePath);

        // Asegurarse de que el tamaño sea un número positivo
        $this->assertGreaterThan(0, $fileSize, 'El archivo está vacío o no se puede acceder correctamente');

        // Ejecutar la función de descarga si el archivo existe
        // Crear un objeto del archivo de prueba
        $usuario = $this->getMockBuilder('Usuario')
                        ->disableOriginalConstructor()
                        ->getMock();
        
        // Configurar el método para que use la ruta del archivo real
        $usuario->method('descargarSolicitud')
                ->willReturnCallback(function ($file) {
                    if (file_exists($file)) {
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                        header('Content-Length: ' . filesize($file));
                        readfile($file);
                        exit();
                    } else {
                        die('El archivo no existe.');
                    }
                });

        // Simular la descarga
        $usuario->getFileForDownload($filePath);

    }
}
?>
