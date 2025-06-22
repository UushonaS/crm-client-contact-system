<?php
session_start();
include 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = 'admin';  // force admin role

    if (empty($username) || empty($password)) {
        $error = "Please enter username and password.";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            // Insert new admin user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $insertStmt->bind_param("sss", $username, $hashed_password, $role);
            if ($insertStmt->execute()) {
                $success = "Admin user created successfully. You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Error creating user.";
            }
            $insertStmt->close();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Signup</title>
<style>
  body { font-family: Arial, sans-serif; background:#f9fafb; padding: 40px; }
  .signup-box { max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
  h2 { margin-bottom: 20px; text-align: center; }
  input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid #ccc; }
  button { width: 100%; padding: 10px; background: #c0392b; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
  button:hover { background: #922b21; }
  .error { background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
  .success { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
</style>
</head>
<body>
<div class="signup-box">
  <h2>Create Admin User</h2>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success"><?= $success ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <input type="text" name="username" placeholder="Username" required autofocus />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit">Create Admin</button>
  </form>
</div>
</body>
</html>
