<?php

use PHPUnit\Framework\TestCase;
use Usuario;

test('descargarSolicitud devuelve true si el archivo existe', function () {
    $usuario = new Usuario();
    $rutaValida = __DIR__ . '/documento_prueba.docx';

    // Simula que el archivo existe
    file_put_contents($rutaValida, 'contenido de prueba');

    expect($usuario->descargarSolicitud($rutaValida))->toBeTrue();

    // Limpia el archivo simulado
    unlink($rutaValida);
});

test('descargarSolicitud devuelve false si el archivo no existe', function () {
    $usuario = new Usuario();
    $rutaInvalida = 'ruta_inexistente.docx';

    expect($usuario->descargarSolicitud($rutaInvalida))->toBeFalse();
});
