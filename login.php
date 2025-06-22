<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Password matches, create session
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            header("Location: dashboard.php"); // redirect after login
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Login</title>
<style>
  /* Basic styling */
  body { font-family: Arial, sans-serif; background:#f9fafb; padding: 40px;}
  .login-box { max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);}
  h2 { margin-bottom: 20px; text-align: center;}
  input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid #ccc;}
  button { width: 100%; padding: 10px; background: #c0392b; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;}
  button:hover { background: #922b21;}
  .error { background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 10px; border-radius: 5px; }

  /* Logo styling */
  .logo {
    display: block;
    margin: 0 auto 20px auto;
    max-width: 150px;
    height: auto;
  }
</style>
</head>
<body>
<div class="login-box">
  <img src="image.png" alt="Logo" class="logo" />
  <h2>Login</h2>
  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST" action="">
    <input type="text" name="username" placeholder="Username" required autofocus />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit">Log In</button>
  </form>
</div>
</body>
</html>
