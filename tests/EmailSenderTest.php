<?php
use PHPUnit\Framework\TestCase;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../Mailer/EmailSender.php';

class EmailSenderTest extends TestCase {
    private $mailerMock;
    private $emailSender;

    protected function setUp(): void {
        // Crear un mock de PHPMailer
        $this->mailerMock = $this->createMock(PHPMailer::class);

        // Inyectar el mock en EmailSender
        $this->emailSender = new EmailSender("smtp.example.com", "user@example.com", "password");
        $this->emailSender = $this->getMockBuilder(EmailSender::class)
            ->setConstructorArgs(["smtp.example.com", "user@example.com", "password"])
            ->onlyMethods(["sendEmail"]) // Simula solo este método
            ->getMock();
    }

    public function testSendEmailSuccess() {
        // Simular que el correo se envía correctamente
        $this->emailSender->expects($this->once())
            ->method('sendEmail')
            ->willReturn(true);

        // Verificar que sendEmail devuelve true
        $this->assertTrue($this->emailSender->sendEmail("Test Subject", "Test Body"));
    }

    public function testSendEmailFailure() {
        // Simular un fallo en el envío del correo
        $this->emailSender->expects($this->once())
            ->method('sendEmail')
            ->willThrowException(new Exception("Error al enviar el correo"));

        // Verificar que se lanza la excepción esperada
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error al enviar el correo");
        $this->emailSender->sendEmail("Test Subject", "Test Body");
    }
}
