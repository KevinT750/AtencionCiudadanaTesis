<?php
session_start();
require_once "../model/Usuario.php"; // Asegúrate de que esta ruta sea correcta

// Verifica si el usuario tiene permisos
if (!isset($_SESSION['usu_nombre']) || $_SESSION['Descargar'] != 1) {
    header("Location: login.html");
    exit();
}

$docx_asig = '../public/document/Solicitud  Estudiantes 2024_Vigente.docx';

// Crear instancia de la clase Usuario
$usuario = new Usuario();

// Llama a la función descargarSolicitud
$usuario->descargarSolicitud($docx_asig);
?>
