<?php
session_start();
if (!isset($_SESSION["user"])) {
   header("Location: login.php");
}
require_once "database.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 900px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .card {
            background: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #444;
        }

        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 5px;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-primary {
            background: #007bff;
            color: #fff;
        }

        .btn-warning {
            background: #ff9800;
            color: #fff;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            .btn {
                width: 100%;
                display: block;
                margin-top: 10px;
            }

            th, td {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Dashboard</h1>
        
        <div class="card">
            <div class="card-header">Upload CSV or Excel File</div>
            <div class="card-body">
                <?php
                if(isset($_SESSION['message'])) {
                    echo '<div class="alert alert-'.$_SESSION['message_type'].'">'.$_SESSION['message'].'</div>';
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                }
                ?>
                
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    <label for="file">Select File (CSV or Excel)</label>
                    <input type="file" name="file" id="file" required>
                    <p class="form-text">Only .csv and .xlsx files are allowed.</p>
                    <button type="submit" name="import" class="btn btn-primary">Upload and Import</button>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">Imported Data</div>
            <div class="card-body table-container">
                <?php
                $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'imported_data'");
                if (mysqli_num_rows($check_table) > 0) {
                    $result = mysqli_query($conn, "SELECT * FROM imported_data");

                    if (mysqli_num_rows($result) > 0) {
                        echo '<table>';
                        echo '<thead><tr>';
                        $field_info = mysqli_fetch_fields($result);
                        foreach ($field_info as $field) {
                            echo '<th>' . htmlspecialchars($field->name) . '</th>';
                        }
                        echo '</tr></thead>';
                        echo '<tbody>';
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            foreach ($row as $value) {
                                echo '<td>' . htmlspecialchars($value) . '</td>';
                            }
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<div class="alert alert-info">No data has been imported yet.</div>';
                    }
                } else {
                    echo '<div class="alert alert-info">No data has been imported yet.</div>';
                }
                ?>
            </div>
        </div>
        
        <a href="logout.php" class="btn btn-warning">Logout</a>
    </div>
</body>
</html>
