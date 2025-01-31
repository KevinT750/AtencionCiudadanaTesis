<?php
use PHPUnit\Framework\TestCase;

class TestDescargarSolicitud extends TestCase {
    // Simula la sesión de un usuario con permisos
    private function iniciarSesion($descargar) {
        $_SESSION['usu_nombre'] = 'usuario_test';
        $_SESSION['Descargar'] = $descargar;
    }

    // Test para verificar si redirige al login cuando no está autenticado o no tiene permisos
    public function testRedirigeAlLoginSinPermisos() {
        unset($_SESSION['usu_nombre']);
        $_SESSION['Descargar'] = 0;
        
        ob_start();
        require_once '../view/descargar.php'; // Asegúrate de que la ruta sea correcta
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Location: login.html', $output);
    }

    // Test para verificar si el archivo de solicitud existe
    public function testArchivoDisponible() {
        $this->iniciarSesion(1); // Permisos para descargar
        
        $filePath = '../public/document/Solicitud Estudiantes 2024_Vigente.docx';
        $this->assertFileExists($filePath);
    }
    
}
