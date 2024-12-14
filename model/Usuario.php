<?php
require_once "../config/Conexion.php";  // Asegúrate de que se incluya el archivo de conexión

class Usuario {
    public function __construct() {}

    public function verificar($usu_login, $usu_clave) {
        try {
            if (empty($usu_login) || empty($usu_clave)) {
                return false;
            }
            
            // Llamamos al procedimiento almacenado SP_Login
            $sql = "call atencion_ciudadana_ist17j.SP_Login('$usu_login', '$usu_clave')";
            $rspta = ejecutarConsulta($sql);
            
            if ($rspta && $rspta->num_rows > 0) {
                // Aquí obtenemos la información de usuario y el rol (Estudiante/Usuario)
                $usuario = $rspta->fetch_object();
                
                $_SESSION['usu_id'] = $usuario->UsuarioID;
                $_SESSION['usu_nombre'] = $usuario->Nombre;
                $_SESSION['usu_login'] = $usu_login;
                
                // Establecer los permisos de acuerdo al rol
                if ($usuario->Rol == 'Estudiante') {
                    $_SESSION['Rol'] = 'Estudiante';
                } else {
                    $_SESSION['Rol'] = 'Secretaria';
                }

                // Establecer permisos para estudiantes o usuarios
                //$this->establecerPermisos($usuario->Rol);

                return $usuario;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error en verificación: " . $e->getMessage());
            return false;
        }
    }

    private function establecerPermisos($rol) {
        // Resetear todos los permisos
        $_SESSION['Escritorio'] = 0;
        $_SESSION['Secretaria'] = 0;
        $_SESSION['Docente'] = 0;
        $_SESSION['Asignaciones'] = 0;
        $_SESSION['Acceso'] = 0;
    
        // Establecer permisos generales
        $_SESSION['Escritorio'] = 1;
    
        // Asignar permisos según el rol
        switch(strtoupper($rol)) {
            case 'SECRETARIA':
                $_SESSION['Secretaria'] = 1;
                break;
            case 'ESTUDIANTE':
                $_SESSION['Estudiante'] = 1;
                break;
            default:
                break;
        }
    }
    
}
?>
