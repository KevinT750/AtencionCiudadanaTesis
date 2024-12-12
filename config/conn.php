<?php 
require_once "global.php";

class Cls_DataConnection {

    function Fn_getConnect($db = 'activos') {
        if ($db === 'activos') {
            $conexion = new mysqli(DB_HOST_ATENCION_CIUDADANA, DB_USERNAME_ATENCION_CIUDADANA, DB_PASSWORD_ATENCION_CIUDADANA, DB_NAME_ATENCION_CIUDADANA);
        } elseif ($db === 'horarios') {
            $conexion = new mysqli(DB_HOST_ESTUDIANTES, DB_USERNAME_ESTUDIANTES, DB_PASSWORD_ESTUDIANTES, DB_NAME_ESTUDIANTES);
        } 

        if ($conexion->connect_errno) {
            echo "Error conectando a la base de datos " . $db . ": " . $conexion->connect_error;
            exit();
        }

        return $conexion;
    }
}
?>