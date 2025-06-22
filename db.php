<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB settings (same as in db.php)
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "crm_app";

// Connect
$conn = new mysqli($host, $user, $pass, $dbname);

// Test connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}
echo "";
?>
