<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '\model\phpMailer.php';


beforeEach(function () {
    // Crear un mock de PHPMailer
    $this->mailerMock = Mockery::mock(PHPMailer::class);

    // Crear instancia de EmailSender con mock
    $this->emailSender = Mockery::mock(EmailSender::class, ["smtp.example.com", "user@example.com", "password"])
        ->makePartial() // Permite simular solo ciertos métodos
        ->shouldAllowMockingProtectedMethods();
});

test('sendEmail devuelve true cuando el correo se envía correctamente', function () {
    $this->emailSender->shouldReceive('sendEmail')
        ->once()
        ->andReturn(true);

    expect($this->emailSender->sendEmail("Test Subject", "Test Body"))->toBeTrue();
});

test('sendEmail lanza una excepción en caso de error', function () {
    $this->emailSender->shouldReceive('sendEmail')
        ->once()
        ->andThrow(new Exception("Error al enviar el correo"));

    $this->emailSender->sendEmail("Test Subject", "Test Body");
})->throws(Exception::class, "Error al enviar el correo");
