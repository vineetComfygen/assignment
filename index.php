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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>User Dashboard</title>
</head>
<body>
    <div class="container">
        <h1>Welcome to Dashboard</h1>
        
        <div class="card mt-4 mb-4">
            <div class="card-header">
                <h4>Upload CSV or Excel File</h4>
            </div>
            <div class="card-body">
                <?php
                if(isset($_SESSION['message'])) {
                    echo '<div class="alert alert-'.$_SESSION['message_type'].'">'.$_SESSION['message'].'</div>';
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                }
                ?>
                
                <form action="upload.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="file" class="form-label">Select File (CSV or Excel)</label>
                        <input type="file" name="file" id="file" class="form-control" required>
                        <div class="form-text">Only .csv and .xlsx files are allowed.</div>
                    </div>
                    <button type="submit" name="import" class="btn btn-primary">Upload and Import</button>
                </form>
            </div>
        </div>
        
        <!-- Display Imported Data -->
        <div class="card mt-4 mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Imported Data</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php
                    // Check if imported_data table exists
                    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'imported_data'");
                    if (mysqli_num_rows($check_table) > 0) {
                        // Get all records from the table
                        $result = mysqli_query($conn, "SELECT * FROM imported_data");
                        
                        if (mysqli_num_rows($result) > 0) {
                            echo '<table class="table table-striped table-bordered">';
                            
                            // Get column names
                            $field_info = mysqli_fetch_fields($result);
                            echo '<thead><tr>';
                            foreach ($field_info as $field) {
                                echo '<th>' . htmlspecialchars($field->name) . '</th>';
                            }
                            echo '</tr></thead>';
                            
                            // Display data rows
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
        </div>
        
        <a href="logout.php" class="btn btn-warning">Logout</a>
    </div>
</body>
</html>