<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendReportEmail($to, $filepath) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // Replace with your email
        $mail->Password = 'your-app-password';    // Replace with your password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('your-email@gmail.com', 'Data Report');
        $mail->addAddress($to);
        
        // Attachment
        $mail->addAttachment($filepath, basename($filepath));
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Data Report ' . date('Y-m-d');
        $mail->Body = 'Please find attached the requested data report.';
        
        $mail->send();
        $_SESSION['message'] = "Report sent successfully to $to";
        $_SESSION['message_type'] = "success";
    } catch (Exception $e) {
        $_SESSION['message'] = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        $_SESSION['message_type'] = "danger";
    }
}