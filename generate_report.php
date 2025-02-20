<?php
session_start();
require_once "database.php";
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function generateReport($format = 'csv') {
    global $conn;
    
    // Create reports directory if not exists
    if (!file_exists('reports')) {
        mkdir('reports', 0777, true);
    }
    
    // Generate filename
    $filename = 'data_report_' . date('Y-m-d_H-i-s') . '.' . $format;
    $filepath = 'reports/' . $filename;
    
    // Get data
    $query = "SELECT * FROM imported_data";
    $result = mysqli_query($conn, $query);
    
    if ($format === 'csv') {
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
    } else {
        // Generate Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Add headers
        $col = 'A';
        $fields = mysqli_fetch_fields($result);
        foreach ($fields as $field) {
            $sheet->setCellValue($col . '1', $field->name);
            $col++;
        }
        
        // Add data
        $row = 2;
        mysqli_data_seek($result, 0);
        while ($data = mysqli_fetch_assoc($result)) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);
    }
    
    return $filepath;
}

// Handle report generation
if (isset($_POST['generate_report'])) {
    $format = $_POST['format'] ?? 'csv';
    
    try {
        $filepath = generateReport($format);
        
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            // If email is provided, send report via email
            require_once 'send_email.php';
            sendReportEmail($_POST['email'], $filepath);
        } else {
            // Direct download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "Error generating report: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
    
    header("Location: index.php");
    exit();
}

