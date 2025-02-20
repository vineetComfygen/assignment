

<?php
$hostname = "localhost";
$dbuser = "root";
$dbPassword = "";
$dbName = "assigmentnew";

$conn = mysqli_connect($hostname, $dbuser, $dbPassword, $dbName);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
