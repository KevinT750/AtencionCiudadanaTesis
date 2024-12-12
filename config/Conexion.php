<?php 
require_once "global.php";
require_once "conn.php";

// Verifica si las funciones no están definidas previamente
if (!function_exists('ejecutarConsultaSP')) {
    // Ejecuta procedimientos almacenados
    function ejecutarConsultaSP($sql, $db = 'atencion_ciudadana') { 
        $Fn = new Cls_DataConnection();
        $Cn = $Fn->Fn_getConnect($db);
        $query = $Cn->query($sql);
        $Cn->close();
        return $query;
    }
}

$conexion = new Cls_DataConnection();
$conexion = $conexion->Fn_getConnect('atencion_ciudadana'); // Conexión a la base de datos 'atencion_ciudadana' por defecto
mysqli_query($conexion, 'SET NAMES "utf8"');

// Verifica si hay errores en la conexión
if (mysqli_connect_errno()) {
    printf("Falló en la conexión con la base de datos: %s\n", mysqli_connect_error());
    exit();
}

if (!function_exists('ejecutarConsulta')) {
    // Ejecuta consultas generales
    function ejecutarConsulta($sql, $db = 'atencion_ciudadana') {
        $conexion = new Cls_DataConnection();
        $Cn = $conexion->Fn_getConnect($db);
        $query = $Cn->query($sql);
        $Cn->close();
        return $query;
    }

    // Ejecuta consultas que devuelven una fila simple
    function ejecutarConsultaSimpleFila($sql, $db = 'atencion_ciudadana') {
        $conexion = new Cls_DataConnection();
        $Cn = $conexion->Fn_getConnect($db);
        $query = $Cn->query($sql);
        $row = $query->fetch_row();
        $Cn->close();
        return $row;
    }

    // Ejecuta consultas de inserción y devuelve el ID generado
    function ejecutarConsulta_retornarID($sql, $db = 'atencion_ciudadana') {
        $conexion = new Cls_DataConnection();
        $Cn = $conexion->Fn_getConnect($db);
        $query = $Cn->query($sql);
        $lastId = $Cn->insert_id;
        $Cn->close();
        return $lastId;
    }

    // Limpia y escapa cadenas
    function limpiarCadena($str, $db = 'atencion_ciudadana') {
        $conexion = new Cls_DataConnection();
        $Cn = $conexion->Fn_getConnect($db);
        $str = mysqli_real_escape_string($Cn, trim($str));
        $Cn->close();
        return htmlspecialchars($str);
    }
}
?>
