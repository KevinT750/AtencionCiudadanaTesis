<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Asegúrate de incluir las clases necesarias de PHPMailer
require_once '../Mailer/PHPMailer/PHPMailer.php';
require_once '../Mailer/PHPMailer/Exception.php';
require_once '../Mailer/PHPMailer/SMTP.php';

class EmailSender {
    private $mailer;

    public function __construct($host, $username, $password, $port, $smtpSecure) {
        // Inicializar PHPMailer
        $this->mailer = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = $host;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $username;
            $this->mailer->Password = $password;
            $this->mailer->SMTPSecure = $smtpSecure;
            $this->mailer->Port = $port;

            // Configuración predeterminada
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';
        } catch (Exception $e) {
            throw new Exception("Error al configurar PHPMailer: " . $e->getMessage());
        }
    }

    public function setFrom($email, $name = '') {
        try {
            $this->mailer->setFrom($email, $name);
        } catch (Exception $e) {
            throw new Exception("Error al configurar el remitente: " . $e->getMessage());
        }
    }

    public function addRecipient($email, $name = '') {
        try {
            $this->mailer->addAddress($email, $name);
        } catch (Exception $e) {
            throw new Exception("Error al agregar destinatario: " . $e->getMessage());
        }
    }

    public function addCC($email, $name = '') {
        try {
            $this->mailer->addCC($email, $name);
        } catch (Exception $e) {
            throw new Exception("Error al agregar CC: " . $e->getMessage());
        }
    }

    public function addBCC($email, $name = '') {
        try {
            $this->mailer->addBCC($email, $name);
        } catch (Exception $e) {
            throw new Exception("Error al agregar BCC: " . $e->getMessage());
        }
    }

    public function addAttachment($filePath, $fileName = '') {
        try {
            $this->mailer->addAttachment($filePath, $fileName);
        } catch (Exception $e) {
            throw new Exception("Error al agregar archivo adjunto: " . $e->getMessage());
        }
    }

    public function sendEmail($subject, $body) {
        try {
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            // Enviar correo
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al enviar el correo: " . $e->getMessage());
        }
    }
}
