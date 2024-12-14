<?php
require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

// Cargar el documento existente
$filePath = 'ruta/al/archivo.docx'; // Ruta al archivo existente
$phpWord = IOFactory::load($filePath);

// Acceder a las secciones y realizar cambios
$section = $phpWord->getSections()[0]; // Obtener la primera sección
$section->addText('Este texto fue agregado usando PHPWord.', ['name' => 'Calibri', 'size' => 11, 'bold' => true]);

// Guardar los cambios en un nuevo archivo
$newFilePath = '../public/document/Solicitud  Estudiantes 2024_Vigente.docx';
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($newFilePath);

echo "Documento editado y guardado en: $newFilePath";
?>
