<?php 
require_once "global.php";
require_once "conn.php";

// Verifica si las funciones no están definidas previamente
if (!function_exists('ejecutarConsultaSP')) {
    // Ejecuta procedimientos almacenados
    function ejecutarConsultaSP($sql, $db = 'atencion_ciudadana') { 
        $Fn = new Cls_DataConnection();
        $Cn = $Fn->Fn_getConnect($db); // Aquí se obtiene la conexión
        $query = $Cn->query($sql); // Ejecuta la consulta
        $Cn->close(); // Cierra la conexión
        return $query;
    }
}

// Conexión por defecto a la base de datos 'atencion_ciudadana'
$conexion = new Cls_DataConnection();
$conexion = $conexion->Fn_getConnect('atencion_ciudadana'); // Conexión a 'atencion_ciudadana'

// Asegúrate de que la conexión es exitosa
if ($conexion->connect_errno) {
    printf("Falló la conexión: %s\n", $conexion->connect_error);
    exit();
}

// Configuración de la codificación
$conexion->query('SET NAMES "utf8"'); // Usar el método query directamente sobre el objeto conexión

// Verifica si las funciones no están definidas previamente
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
