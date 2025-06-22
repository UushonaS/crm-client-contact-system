<?php
require 'vendor/autoload.php'; // PHPMailer autoload
include 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$name = $surname = $email = $description = $type = "";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $surname = trim($_POST['surname']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $description = trim($_POST['description']);
    $type = trim($_POST['type']);

    if (empty($surname) || empty($name) || empty($email)) {
        $message = "❌ Surname, name, and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM contacts WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "❌ Email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO contacts (name, surname, email, description, type) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $surname, $email, $description, $type);

            if ($stmt->execute()) {
                $contact_id = $stmt->insert_id;

                // PHPMailer to send email silently
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'selmauushona480@gmail.com';  // Change to your Gmail
                    $mail->Password = 'xonbqnqfxqlnahkt';    // Use your Gmail App Password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('selmauushona480@gmail.com', 'Binary City');
                    $mail->addAddress($email, $name . ' ' . $surname);
                    $mail->isHTML(true);
                    $mail->Subject = "Welcome to Binary City";
                    $mail->Body    = "Hi <strong>$name $surname</strong>,<br><br>You've been successfully added to the Binary City CRM system. Welcome aboard!<br><br>Regards,<br>Binary City Team";

                    $mail->send();

                    // Mark email_sent = 1
                    $updateStmt = $conn->prepare("UPDATE contacts SET email_sent = 1 WHERE id = ?");
                    $updateStmt->bind_param("i", $contact_id);
                    $updateStmt->execute();

                    $message = "✅ Contact saved and email sent!";
                } catch (Exception $e) {
                    // Ignore email errors, just confirm contact saved
                    $message = "✅ Contact saved!";
                }
            } else {
                $message = "❌ Failed to save contact.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Contact</title>
  <style>
    body {
      background: #dfe6e9;
      font-family: 'Segoe UI', sans-serif;
      padding: 20px;
      display: flex;
      justify-content: center;
    }

    .container {
      background: #ffffff;
      padding: 20px;
      border-radius: 8px;
      max-width: 420px;
      width: 100%;
      box-shadow: 0 3px 12px rgba(0,0,0,0.1);
    }

    img.logo {
      max-height: 42px;
      display: block;
      margin: 0 auto 12px;
    }

    h2 {
      text-align: center;
      margin-bottom: 18px;
    }

    .message {
      padding: 10px;
      margin-bottom: 12px;
      border-radius: 5px;
      font-size: 14px;
    }

    .success {
      background: #d4edda;
      color: #155724;
    }

    .error {
      background: #f8d7da;
      color: #721c24;
    }

    label {
      font-weight: 600;
      font-size: 0.9rem;
    }

    input, textarea {
      width: 100%;
      padding: 8px;
      margin-top: 4px;
      margin-bottom: 12px;
      border-radius: 4px;
      border: 1px solid #ccc;
      font-size: 0.9rem;
    }

    button {
      background-color: #c0392b;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 4px;
      font-size: 0.9rem;
      width: 100%;
      font-weight: bold;
    }

    button:hover {
      background-color: #922b21;
    }

    a.back-link {
      display: block;
      text-align: center;
      margin-top: 16px;
      font-size: 0.85rem;
      color: #c0392b;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="container">
  <img src="image.png" class="logo" alt="Logo">
  <h2>Add New Contact</h2>

  <?php if ($message): ?>
    <div class="message <?= str_starts_with($message, '✅') ? 'success' : 'error' ?>">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="">
    <label>Surname: *</label>
    <input type="text" name="surname" value="<?= htmlspecialchars($surname) ?>" required>

    <label>Name: *</label>
    <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>

    <label>Email: *</label>
    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

    <label>Description:</label>
    <textarea name="description"><?= htmlspecialchars($description) ?></textarea>

    <label>Type:</label>
    <input type="text" name="type" value="<?= htmlspecialchars($type) ?>">

    <button type="submit">Save Contact</button>
  </form>

  <a class="back-link" href="contacts.php">← Back to Contacts List</a>
</div>

</body>
</html>
