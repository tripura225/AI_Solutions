<?php
require_once __DIR__ . '/../../src/PHPMailer.php';
require_once __DIR__ . '/../../src/SMTP.php';
require_once __DIR__ . '/../../src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendReplyEmail($to, $name, $reply_message, $original_message) {
    $mail = new PHPMailer(true);
    
    try {
        // Enable verbose debug output (remove after testing)
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tripurabhandari225@gmail.com';     
        $mail->Password   = 'jeceynvulbblcztv'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Timeout settings
        $mail->Timeout = 30;
        
        // Sender & Recipient
        $mail->setFrom('tripurabhandari225@gmail.com', 'AI Solutions');
        $mail->addAddress($to, $name);
        
        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'Reply from AI Solutions - Regarding your inquiry';
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0F4C5C; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .message { background: white; padding: 15px; border-left: 4px solid #E76F51; margin: 15px 0; }
                .footer { text-align: center; padding: 15px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>AI Solutions</h2>
                </div>
                <div class="content">
                    <p>Dear ' . htmlspecialchars($name) . ',</p>
                    <div class="message">
                        <p><strong>Reply to your inquiry:</strong></p>
                        <p>' . nl2br(htmlspecialchars($reply_message)) . '</p>
                    </div>
                    <p>Original message:</p>
                    <div class="message" style="background:#f0f0f0">
                        <p><strong>You wrote:</strong></p>
                        <p>' . nl2br(htmlspecialchars($original_message)) . '</p>
                    </div>
                    <p>Best regards,<br><strong>AI Solutions Team</strong></p>
                </div>
                <div class="footer">
                    <p>© 2026 AI Solutions. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        $mail->send();
        return ['success' => true, 'message' => 'Email sent successfully'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Email failed: ' . $mail->ErrorInfo];
    }
}
?>