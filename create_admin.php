<?php
include 'db.php';

$username = 'admin';      // change username
$password = '12345'; // change password
$role = 'admin';

// Hash password securely
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $hashed_password, $role);

if ($stmt->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $conn->error;
}

