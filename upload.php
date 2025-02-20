<?php
session_start();
require_once "database.php";

// Consistent session check
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['import'])) {
    // Validate file upload
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        setError("Error: No file uploaded or upload error occurred.");
        exit();
    }

    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Validate file extension
    $allowed_exts = ['csv', 'xlsx'];
    if (!in_array($file_ext, $allowed_exts)) {
        setError("Error: Only CSV and Excel files are allowed.");
        exit();
    }

    // Create secure filename
    $secure_filename = date('Y-m-d-H-i-s') . '-' . uniqid() . '.' . $file_ext;
    
    // Create user-specific upload directory
    $upload_dir = "uploads/user_" . $user_id . "/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_path = $upload_dir . $secure_filename;

    // Move uploaded file
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Store file information in database
        $stmt = $conn->prepare("INSERT INTO uploaded_files (user_id, file_name, file_path, uploaded_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $file_name, $file_path);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "File uploaded successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            setError("Error: Failed to record file in database.");
            unlink($file_path); // Remove uploaded file if database insert fails
        }
        $stmt->close();
    } else {
        setError("Error: Failed to save the uploaded file.");
    }
}

header("Location: index.php");
exit();

function setError($message) {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = "danger";
}
?>