<?php
// File: email_config.php

// Email configuration settings
define('SMTP_HOST', 'sandbox.smtp.mailtrap.io');
define('SMTP_PORT', 2525);
define('SMTP_USERNAME', '8ad4bbd86e4475'); // Replace with your Gmail
define('SMTP_PASSWORD', 'dd7ef369533dd9'); // Replace with generated app password
define('SMTP_FROM_EMAIL', 'vineetsomani11@gmail.com');
define('SMTP_FROM_NAME', 'Vineet Somani');

function sendEmail($to, $subject, $body, $attachment = null) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        // Add attachment if provided
        if ($attachment && file_exists($attachment)) {
            $mail->addAttachment($attachment, basename($attachment));
        }
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>