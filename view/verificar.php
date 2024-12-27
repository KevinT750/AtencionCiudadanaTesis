<?php
session_start();

// Verificar si las sesiones existen
if (isset($_SESSION['doc_ids']) && isset($_SESSION['cedula_ids'])) {
    echo "<h3>Contenido de la sesión 'doc_ids':</h3>";
    echo "<pre>"; // La etiqueta <pre> permite mostrar el resultado de manera más legible
    print_r($_SESSION['doc_ids']); // Mostrar el contenido de doc_ids
    echo "</pre>";

    echo "<h3>Contenido de la sesión 'cedula_ids':</h3>";
    echo "<pre>";
    print_r($_SESSION['cedula_ids']); // Mostrar el contenido de cedula_ids
    echo "</pre>";
} else {
    echo "Las variables de sesión 'doc_ids' y/o 'cedula_ids' no están definidas.";
}
?>
