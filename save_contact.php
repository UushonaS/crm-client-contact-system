<?php
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug: show all POST data
// Uncomment the next 3 lines to see form data
// echo "<pre>";
// print_r($_POST);
// echo "</pre>"; exit;

// Safely get form data
$name        = isset($_POST['name']) ? trim($_POST['name']) : null;
$surname     = isset($_POST['surname']) ? trim($_POST['surname']) : null;
$email       = isset($_POST['email']) ? trim($_POST['email']) : null;
$description = isset($_POST['description']) ? trim($_POST['description']) : null;
$type        = isset($_POST['type']) ? trim($_POST['type']) : null;

// Check required fields
if (!$name || !$surname || !$email) {
    die("❌ Name, Surname, and Email are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("❌ Invalid email address.");
}

// Check for duplicate email
$check = $conn->prepare("SELECT id FROM contacts WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    die("❌ This email is already registered.");
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO contacts (name, surname, email, description, type) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $surname, $email, $description, $type);

if ($stmt->execute()) {
    echo "✅ Contact saved successfully!";
    // Optionally redirect: header("Location: contacts.php"); exit();
} else {
    echo "❌ Failed to save: " . $stmt->error;
}
?>
