<?php
session_start();
require_once "database.php";

// Check if the user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Check if form was submitted
if (isset($_POST['import'])) {
    // Check if file was uploaded without errors
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_type = $_FILES['file']['type'];
        
        // Get file extension
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Allowed extensions
        $allowed_exts = ['csv', 'xlsx'];
        
        // Validate file extension
        if (!in_array($file_ext, $allowed_exts)) {
            $_SESSION['message'] = "Error: Only CSV and Excel files are allowed.";
            $_SESSION['message_type'] = "danger";
            header("Location: index.php");
            exit();
        }
        
        // Process the file based on its extension
        if ($file_ext == 'csv') {
            processCSV($file_tmp, $conn);
        } else if ($file_ext == 'xlsx') {
            processExcel($file_tmp, $conn);
        }
        
    } else {
        $_SESSION['message'] = "Error: " . $_FILES['file']['error'];
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit();
    }
} else {
    // If someone accesses this page directly without submitting the form
    header("Location: index.php");
    exit();
}

/**
 * Process CSV file and import data to database
 */
function processCSV($file_path, $conn) {
    // Open the file
    $handle = fopen($file_path, "r");
    
    if ($handle !== FALSE) {
        // Read the first row as headers
        $headers = fgetcsv($handle, 1000, ",");
        
        // Create table if it doesn't exist
        $table_name = "imported_data";
        createTableIfNotExists($conn, $table_name, $headers);
        
        // Counter for successful inserts
        $inserted_rows = 0;
        
        // Read data rows
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) == count($headers)) {
                // Prepare the data for insertion
                $escaped_values = array_map(function($value) use ($conn) {
                    return "'" . mysqli_real_escape_string($conn, $value) . "'";
                }, $data);
                
                $values_str = implode(", ", $escaped_values);
                
                // Insert data into the table
                $sql = "INSERT INTO {$table_name} VALUES ({$values_str})";
                
                if (mysqli_query($conn, $sql)) {
                    $inserted_rows++;
                }
            }
        }
        
        fclose($handle);
        
        $_SESSION['message'] = "Successfully imported {$inserted_rows} records from CSV file.";
        $_SESSION['message_type'] = "success";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['message'] = "Error: Could not open the CSV file.";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit();
    }
}

/**
 * Process Excel file and import data to database
 * Requires PhpSpreadsheet library
 */// Replace the processExcel function with this version that uses simple CSV handling
 function processExcel($file_path, $conn) {
    // Since we don't have PhpSpreadsheet installed, we'll use a simpler approach
    
    $_SESSION['message'] = "Note: Excel processing is using simplified method. Consider installing PhpSpreadsheet for full Excel support.";
    $_SESSION['message_type'] = "warning";
    
    // Define table name before any conditional logic
    $table_name = "imported_data";
    
    // For simple Excel files, we can try to read them as CSV
    $handle = fopen($file_path, "r");
    
    if ($handle !== FALSE) {
        // Read the first row as headers
        $headers = [];
        $first_row = true;
        $inserted_rows = 0; // Initialize the counter
        
        // Read line by line
        while (($line = fgets($handle)) !== false) {
            // Split the line by commas or tabs (common Excel export formats)
            $data = str_getcsv($line, ",");
            
            if ($first_row) {
                $headers = $data;
                $first_row = false;
                
                // Create table if it doesn't exist
                createTableIfNotExists($conn, $table_name, $headers);
            } else {
                // Check if data matches headers count
                if (count($data) == count($headers)) {
                    // Prepare the data for insertion
                    $escaped_values = array_map(function($value) use ($conn) {
                        return "'" . mysqli_real_escape_string($conn, $value) . "'";
                    }, $data);
                    
                    $values_str = implode(", ", $escaped_values);
                    
                    // Insert data into the table
                    $sql = "INSERT INTO {$table_name} VALUES ({$values_str})";
                    
                    if (mysqli_query($conn, $sql)) {
                        $inserted_rows++;
                    }
                }
            }
        }
        
        fclose($handle);
        
        $_SESSION['message'] .= " Successfully imported {$inserted_rows} records from Excel file.";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['message'] = "Error: Could not open the Excel file.";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit();
    }
}

/**
 * Create a table based on CSV/Excel headers if it doesn't exist
 */
function createTableIfNotExists($conn, $table_name, $headers) {
    // Check if table exists
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE '{$table_name}'");
    if (mysqli_num_rows($check_table) == 0) {
        // Create table with columns based on headers
        $columns = [];
        foreach ($headers as $header) {
            // Clean header name for SQL column name
            $column_name = preg_replace('/[^a-zA-Z0-9_]/', '_', $header);
            $columns[] = "`{$column_name}` VARCHAR(255)";
        }
        
        $columns_str = implode(", ", $columns);
        
        $create_table_sql = "CREATE TABLE {$table_name} ({$columns_str})";
        mysqli_query($conn, $create_table_sql);
    }
}