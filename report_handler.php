<?php
// File: report_handler.php
session_start();
require_once "database.php";
require 'vendor/autoload.php'; // For PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Generate CSV report from database
 * @return string filepath
 */
function generateCSVReport() {
    global $conn;
    
    // Create reports directory if it doesn't exist
    if (!file_exists('reports')) {
        mkdir('reports', 0777, true);
    }
    
    // Generate filename
    $filename = 'data_report_' . date('Y-m-d_H-i-s') . '.csv';
    $filepath = 'reports/' . $filename;
    
    // Get data
    $query = "SELECT * FROM imported_data";
    $result = mysqli_query($conn, $query);
    
    // Generate CSV
    $file = fopen($filepath, 'w');
    
    // Add headers
    $headers = [];
    $fields = mysqli_fetch_fields($result);
    foreach ($fields as $field) {
        $headers[] = $field->name;
    }
    fputcsv($file, $headers);
    
    // Add data
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($file, $row);
    }
    
    fclose($file);
    return $filepath;
}

/**
 * Send email with report attachment
 * @param string $to recipient email
 * @param string $filepath path to report file
 * @return bool success status
 */
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
        return true;
    } catch (Exception $e) {
        $_SESSION['message'] = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        $_SESSION['message_type'] = "danger";
        return false;
    }
}

// Handle report generation request
if (isset($_POST['generate_report'])) {
    try {
        $filepath = generateCSVReport();
        
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            // Send via email if email address is provided
            sendReportEmail($_POST['email'], $filepath);
            header("Location: index.php");
            exit();
        } else {
            // Direct download if no email provided
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "Error generating report: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit();
    }
}
?>