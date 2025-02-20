<?php
session_start();
require_once "database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Handle File Deletion
if (isset($_POST["delete_file"])) {
    $file_id = $_POST["file_id"];
    $query = "SELECT * FROM uploaded_files WHERE id = $file_id AND user_id = $user_id";
    $result = mysqli_query($conn, $query);
    $file = mysqli_fetch_assoc($result);

    if ($file) {
        unlink($file["file_path"]); // Delete file from server
        $deleteQuery = "DELETE FROM uploaded_files WHERE id = $file_id";
        mysqli_query($conn, $deleteQuery);
    }

    header("Location: index.php");
    exit();
}
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
            font-family: 'Arial', sans-serif;
        }
        body {
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }
        .navbar {
            background: #007bff;
            padding: 10px 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
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
        input[type="file"], input[type="text"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
            cursor: pointer;
        }
        .btn-primary {
            background: #007bff;
            color: #fff;
        }
        .btn-danger {
            background: #dc3545;
            color: #fff;
        }
        .file-details {
            display: none;
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <span>Dashboard</span>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <h1>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?></h1>

        <div class="card">
            <h2>Upload CSV</h2>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <input type="file" name="file" required>
                <button type="submit" name="import" class="btn btn-primary">Upload</button>
            </form>
        </div>

        <div class="card">
            <h2>Your Uploaded Files</h2>
            <table>
                <tr>
                    <th>File Name</th>
                    <th>Uploaded At</th>
                    <th>Actions</th>
                </tr>

                <?php
                $query = "SELECT * FROM uploaded_files WHERE user_id = $user_id ORDER BY uploaded_at DESC";
                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td><a href='#' class='file-link' data-file='{$row['file_path']}'>{$row['file_name']}</a></td>
                            <td>{$row['uploaded_at']}</td>
                            <td>
                                <a href='{$row['file_path']}' download class='btn btn-primary'>Download</a>
                                <form method='post' style='display:inline;'>
                                    <input type='hidden' name='file_id' value='{$row['id']}'>
                                    <button type='submit' name='delete_file' class='btn btn-danger'>Delete</button>
                                </form>
                            </td>
                          </tr>";
                }
                ?>
            </table>
        </div>

        <div class="card">
            <h2>Search & Filter</h2>
            <input type="text" id="searchInput" placeholder="Search...">
            <select id="filterColumn">
                <option value="0">Name</option>
                <option value="1">Email</option>
                <option value="2">ID</option>
                <option value="3">Password</option>
            </select>
        </div>

        <div class="card file-details" id="fileDetails">
            <h2>File Details</h2>
            <table id="fileTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>ID</th>
                        <th>Password</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".file-link").forEach(link => {
                link.addEventListener("click", function (e) {
                    e.preventDefault();
                    const filePath = this.getAttribute("data-file");

                    fetch(filePath)
                        .then(response => response.text())
                        .then(data => {
                            const lines = data.split("\n").slice(1);
                            const tableBody = document.querySelector("#fileTable tbody");
                            tableBody.innerHTML = "";

                            lines.forEach(line => {
                                if (line.trim() === "") return;
                                const [name, email, id, password] = line.split(",");
                                const row = `<tr>
                                    <td>${name}</td>
                                    <td>${email}</td>
                                    <td>${id}</td>
                                    <td>${password}</td>
                                </tr>`;
                                tableBody.innerHTML += row;
                            });

                            document.getElementById("fileDetails").style.display = "block";
                        });
                });
            });

            document.getElementById("searchInput").addEventListener("input", function () {
                const value = this.value.toLowerCase();
                document.querySelectorAll("#fileTable tbody tr").forEach(row => {
                    row.style.display = row.children[document.getElementById("filterColumn").value].textContent.toLowerCase().includes(value) ? "" : "none";
                });
            });
        });
    </script>
</body>
</html>
