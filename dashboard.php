<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Optional: Only allow admin users to access this page
if ($_SESSION['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Dashboard</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f9fafb;
    padding: 20px;
  }

  .container {
    max-width: 700px;
    margin: auto;
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
  }

  img.logo {
    max-width: 120px;
    display: block;
    margin: 0 auto 20px auto;
  }

  h1 {
    color: #c0392b;
    margin-bottom: 15px;
    text-align: center;
  }

  p {
    text-align: center;
    margin-bottom: 25px;
  }

  ul.function-list {
    list-style: none;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 12px;
  }

  ul.function-list li {
    flex: 0 1 45%;
    text-align: center;
  }

  ul.function-list a {
    display: block;
    background-color: #c0392b;
    color: white;
    padding: 8px 12px;
    border-radius: 4px;
    font-weight: 600;
    text-decoration: none;
    font-size: 0.92rem;
    transition: background-color 0.3s;
  }

  ul.function-list a:hover {
    background-color: #922b21;
  }

  .logout-btn {
    display: block;
    margin: 30px auto 0 auto;
    padding: 10px 20px;
    background-color: #7f8c8d;
    color: white;
    border-radius: 4px;
    text-align: center;
    font-weight: bold;
    text-decoration: none;
    width: 200px;
  }

  .logout-btn:hover {
    background-color: #636e72;
  }
</style>
</head>
<body>
  <div class="container">
    <img src="image.png" alt="Logo" class="logo" />
    <h1>Welcome, <?= $username ?> (Admin)</h1>
    <p>Choose an action below:</p>

    <ul class="function-list">
      <li><a href="clients.php">📁 Manage Clients</a></li>
      <li><a href="contacts.php">👤 Manage Contacts</a></li>
      <li><a href="link_client_contacts.php">🔗 Link Clients & Contacts</a></li>
      <li><a href="add_client.php">➕ Add Client</a></li>
      <li><a href="add_contact.php">➕ Add Contact</a></li>
      <li><a href="signup_admin.php">👨‍💼 Add New Admin</a></li>
    </ul>

    <a href="logout.php" class="logout-btn">Logout</a>
  </div>
</body>
</html>
