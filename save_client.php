<?php
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '';

if ($name === '') {
    die("❌ Client name is required.");
}

// Generate 3-letter alpha prefix
$prefix = strtoupper(preg_replace('/[^A-Z]/', '', substr($name, 0, 3)));
$prefix = str_pad($prefix, 3, 'A'); // pad if less than 3 chars

// Generate numeric suffix starting at 001, increment to find unique code
$num = 1;
do {
    $client_code = $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
    $stmt = $conn->prepare("SELECT id FROM clients WHERE client_code = ?");
    $stmt->bind_param("s", $client_code);
    $stmt->execute();
    $stmt->store_result();
    $num++;
} while ($stmt->num_rows > 0);

$stmt = $conn->prepare("INSERT INTO clients (name, description, type, client_code) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $description, $type, $client_code);

if ($stmt->execute()) {
    header("Location: clients.php");
    exit();
} else {
    echo "❌ Error saving client: " . $stmt->error;
}
