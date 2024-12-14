<?php 
require_once 'global.php';
class Cls_DataConnection {
    // Método para obtener la conexión a la base de datos
    public function Fn_getConnect($db = 'atencion_ciudadana') {
        if ($db === 'atencion_ciudadana') {
            $conexion = new mysqli(DB_HOST_ATENCION_CIUDADANA, DB_USERNAME_ATENCION_CIUDADANA, DB_PASSWORD_ATENCION_CIUDADANA, DB_NAME_ATENCION_CIUDADANA);
        } elseif ($db === 'estudiantes') {
            $conexion = new mysqli(DB_HOST_ESTUDIANTES, DB_USERNAME_ESTUDIANTES, DB_PASSWORD_ESTUDIANTES, DB_NAME_ESTUDIANTES);
        }

        // Verificar si hubo un error en la conexión
        if ($conexion->connect_errno) {
            echo "Error al conectar a la base de datos " . $db . ": " . $conexion->connect_error;
            exit();
        }

        return $conexion;
    }
}

?>