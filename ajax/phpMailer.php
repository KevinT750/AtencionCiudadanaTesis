<?php
ob_start();
session_start();

if (!isset($_SESSION['usu_nombre'])) {
    header("Location: login.html");
    exit();
}

require_once '../model/phpMailer.php';

$emailSender = new EmailSender(
    'smtp.gmail.com',    // Servidor SMTP de Gmail
    'kevinteran750@gmail.com',  // Tu usuario SMTP
    'icyc ctli dfgt ibbj',  // Tu contraseña SMTP o contraseña de aplicación (si usas 2FA)
    465,   // Puerto para SSL
    'ssl'  // Seguridad: SSL en lugar de TLS
);


if (isset($_GET['op'])) {
    $op = $_GET['op'];

    switch ($op){
        case 'enviarCorreo':
            if (isset($_POST['motivoSolicitud'], $_POST['correosSeleccionados'], $_POST['comentario'], $_POST['columna2'], $_POST['columna3'])) {
                $motivoSolicitud = $_POST['motivoSolicitud'];
                $correosSeleccionados = $_POST['correosSeleccionados'];
                $comentario = $_POST['comentario'];
                $columna2 = $_POST['columna2'];
                $columna3 = $_POST['columna3'];
        
                // Validar que las direcciones de correo sean válidas
                $correosValidos = [];
                foreach ($correosSeleccionados as $correo) {
                    if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                        $correosValidos[] = $correo;
                    } else {
                        echo json_encode([
                            'estado' => false,
                            'error' => 'Dirección de correo inválida: ' . $correo
                        ]);
                        exit();
                    }
                }
        
                // Construir el cuerpo del correo con enlaces a los archivos Drive
                $body = "<p><strong>Motivo de la solicitud:</strong> $motivoSolicitud</p>";
                $body .= "<p><strong>Comentario:</strong> $comentario</p>";
                $body .= "<p><strong>Archivos relacionados:</strong></p>";
                $body .= "<ul>";
                $body .= "<li><a href='https://drive.google.com/file/d/$columna2/view' target='_blank'>Archivo 1</a></li>";
                $body .= "<li><a href='https://drive.google.com/file/d/$columna3/view' target='_blank'>Archivo 2</a></li>";
                $body .= "</ul>";
        
                try {
                    // Configurar el correo
                    $emailSender->setFrom('kevinteran750@gmail.com', 'Instituto 17 de Julio');
                    foreach ($correosValidos as $correo) {
                        $emailSender->addRecipient($correo);
                    }
        
                    // Enviar el correo
                    $subject = 'Solicitud de Estudiante';
                    $emailSender->sendEmail($subject, $body);
        
                    echo json_encode([
                        'estado' => true,
                        'mensaje' => 'Correo enviado correctamente.'
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'estado' => false,
                        'error' => 'Error al enviar el correo: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'estado' => false,
                    'error' => 'Faltan datos para enviar el correo.'
                ]);
            }
            break;
        

        default:
            echo json_encode([
                'estado' => false,
                'error' => 'Operación no válida.'
            ]);
            break;
    }

} else {
    echo json_encode([
        'estado' => false,
        'error' => 'No se especificó la operación.'
    ]);
}
?>
