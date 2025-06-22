<?php
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$contact_id = 0;
$name = "";
$surname = "";
$email = "";
$description = "";
$type = "";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);
    $description = trim($_POST['description']);
    $type = trim($_POST['type']);

    if (empty($name) || empty($surname) || empty($email)) {
        $message = "❌ Name, surname, and email are required.";
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
                $message = "✅ Contact saved successfully!";
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
  margin: 0;
  padding: 15px;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  min-height: 100vh;
}

.container {
  background: #ffffff;
  padding: 16px 18px;
  max-width: 420px;
  width: 100%;
  border-radius: 8px;
  box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
  box-sizing: border-box;
}

img.logo {
  max-height: 42px;
  display: block;
  margin: 0 auto 12px;
}

h2 {
  text-align: center;
  color: #2c3e50;
  margin: 0 0 14px;
  font-size: 1.1rem;
}

.message {
  padding: 8px 10px;
  border-radius: 5px;
  margin-bottom: 12px;
  font-size: 13px;
}

.success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

label {
  font-weight: 600;
  display: block;
  margin-top: 8px;
  font-size: 0.85rem;
}

input, textarea {
  width: 100%;
  padding: 6px 8px;
  margin-top: 3px;
  border-radius: 4px;
  border: 1px solid #ccc;
  font-size: 0.85rem;
  box-sizing: border-box;
}

textarea {
  resize: vertical;
  min-height: 50px;
}

button {
  background: #c0392b; /* red */
  color: white;
  border: none;
  padding: 9px 14px;
  margin-top: 14px;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  font-size: 0.9rem;
  width: 100%;
}

button:hover {
  background: #922b21; /* darker red */
}

a.back-link {
  display: block;
  text-align: center;
  margin-top: 16px;
  color: #c0392b;
  font-size: 0.85rem;
  text-decoration: none;
}

a.back-link:hover {
  text-decoration: underline;
}


  </style>
  <script>
    function showTab(tabId) {
      document.querySelectorAll(".tab").forEach(tab => tab.classList.remove("active"));
      document.getElementById(tabId).classList.add("active");
      document.querySelectorAll(".tabs button").forEach(btn => btn.classList.remove("active"));
      document.querySelector(`button[data-target="${tabId}"]`).classList.add("active");
    }

    function validateContactForm() {
      const name = document.forms["contactForm"]["name"].value.trim();
      const surname = document.forms["contactForm"]["surname"].value.trim();
      const email = document.forms["contactForm"]["email"].value.trim();
      if (!name || !surname || !email) {
        alert("❌ Please fill in all required fields.");
        return false;
      }
      return true;
    }

    window.onload = function () {
      showTab('tab_general');
    };
  </script>
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

  <div class="tabs">
    <button type="button" data-target="tab_general" onclick="showTab('tab_general')">General</button>
    <?php if ($contact_id): ?>
      <button type="button" data-target="tab_clients" onclick="showTab('tab_clients')">Client(s)</button>
    <?php endif; ?>
  </div>

  <form name="contactForm" method="POST" action="" onsubmit="return validateContactForm()">
    <div id="tab_general" class="tab">
      <label>Name: *</label>
      <input type="text" name="name" value="<?= htmlspecialchars($name) ?>">

      <label>Surname: *</label>
      <input type="text" name="surname" value="<?= htmlspecialchars($surname) ?>">

      <label>Email: *</label>
      <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">

      <label>Description:</label>
      <textarea name="description"><?= htmlspecialchars($description) ?></textarea>

      <label>Type:</label>
      <input type="text" name="type" value="<?= htmlspecialchars($type) ?>">

      <button type="submit">Save Contact</button>
    </div>
  </form>

  <?php if ($contact_id): ?>
    <div id="tab_clients" class="tab">
      <h3>Linked Clients</h3>
      <table>
        <thead>
          <tr>
            <th>Client Name</th>
            <th>Client Code</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $stmt = $conn->prepare("SELECT c.id, c.name, c.client_code FROM clients c JOIN client_contact_links l ON c.id = l.client_id WHERE l.contact_id = ?");
        $stmt->bind_param("i", $contact_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0):
          while ($row = $res->fetch_assoc()):
        ?>
          <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['client_code']) ?></td>
            <td><a class="unlink-link" href="unlink_link.php?contact_id=<?= $contact_id ?>&client_id=<?= $row['id'] ?>&from=contact" onclick="return confirm('Are you sure you want to unlink this client?')">Unlink</a></td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="3" style="text-align:center; font-style: italic;">No clients found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <a class="back-link" href="contacts.php">← Back to Contacts List</a>
</div>

</body>
</html>
