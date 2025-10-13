<?php
// Configuración para el envío de correos electrónicos
// Este archivo contiene la configuración SMTP para el sistema de recuperación de contraseñas

require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';
require_once '../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;
    private $config;
    
    public function __construct() {
        $this->config = include 'email_config.php';
        $this->mailer = new PHPMailer(true);
        $this->configureSMTP();
    }
    
    private function configureSMTP() {
        try {
            // Configuración del servidor SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host       = $this->config['smtp']['host'];
            $this->mailer->SMTPAuth   = $this->config['smtp']['auth'];
            $this->mailer->Username   = $this->config['smtp']['username'];
            $this->mailer->Password   = $this->config['smtp']['password'];
            $this->mailer->SMTPSecure = $this->config['smtp']['encryption'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $this->mailer->Port       = $this->config['smtp']['port'];
            
            // Configuración general
            $this->mailer->setFrom($this->config['from']['email'], $this->config['from']['name']);
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            error_log("Error configurando SMTP: " . $e->getMessage());
        }
    }
    
    public function sendPasswordResetEmail($to, $name, $resetToken) {
        try {
            $this->mailer->addAddress($to, $name);
            
            $this->mailer->Subject = 'Recuperación de Contraseña - ' . $this->config['app']['name'];
            
            // URL completa para el reset
            $resetUrl = $this->config['app']['url'] . "/Admin/reset_password.php?token=" . $resetToken;
            
            $this->mailer->Body = $this->getEmailTemplate($name, $resetUrl);
            
            $this->mailer->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
            return false;
        } finally {
            $this->mailer->clearAddresses();
        }
    }
      private function getEmailTemplate($name, $resetUrl) {
        $appName = $this->config['app']['name'];
        $supportEmail = $this->config['app']['support_email'];
        $expiryHours = $this->config['password_reset']['expiry_hours'];
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Recuperación de Contraseña</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 30px; background-color: #f8f9fa; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin: 20px 0; font-weight: bold; }
                .button:hover { opacity: 0.9; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
                .warning { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 5px; margin: 15px 0; }
                .url-box { word-break: break-all; background-color: #e9ecef; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 14px; }
                .icon { font-size: 2em; margin-bottom: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='icon'>🔐</div>
                    <h1>" . htmlspecialchars($appName) . "</h1>
                    <h2>Recuperación de Contraseña</h2>
                </div>
                <div class='content'>
                    <p>Hola <strong>" . htmlspecialchars($name) . "</strong>,</p>
                    
                    <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en el " . htmlspecialchars($appName) . ".</p>
                    
                    <p>Para restablecer tu contraseña de forma segura, haz clic en el siguiente botón:</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . $resetUrl . "' class='button'>🔑 Restablecer Contraseña</a>
                    </div>
                    
                    <p>Si el botón no funciona, puedes copiar y pegar el siguiente enlace en tu navegador:</p>
                    <div class='url-box'>" . $resetUrl . "</div>
                    
                    <div class='warning'>
                        <strong>⚠️ Información importante de seguridad:</strong>
                        <ul style='margin: 10px 0; padding-left: 20px;'>
                            <li>Este enlace expirará en <strong>" . $expiryHours . " hora(s)</strong> por seguridad.</li>
                            <li>Solo puede ser usado una vez.</li>
                            <li>Si no solicitaste este cambio, ignora este correo.</li>
                            <li>Tu contraseña actual seguirá siendo válida hasta que la cambies.</li>
                        </ul>
                    </div>
                    
                    <p>Si tienes problemas o no solicitaste este cambio, contacta al administrador del sistema en: <strong>" . htmlspecialchars($supportEmail) . "</strong></p>
                    
                    <p style='margin-top: 30px;'>Saludos,<br>
                    <strong>Equipo de " . htmlspecialchars($appName) . "</strong></p>
                </div>
                <div class='footer'>
                    <p>📧 Este es un correo automático, por favor no respondas a este mensaje.</p>
                    <p>&copy; " . date('Y') . " " . htmlspecialchars($appName) . ". Todos los derechos reservados.</p>
                    <p style='font-size: 10px; color: #999;'>Enviado el " . date('d/m/Y H:i:s') . "</p>
                </div>
            </div>
        </body>
        </html>";
    }
}

// Función helper para generar tokens seguros
function generateSecureToken($length = 64) {
    return bin2hex(random_bytes($length / 2));
}

// Función helper para validar email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
?>
