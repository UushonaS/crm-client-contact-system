<?php 
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$client_id = 0;
$name = "";
$description = "";
$type = "";
$client_code = "";
$message = "";

// Generate client code
function generateClientCode($conn, $clientName) {
    $words = preg_split('/\s+/', strtoupper(trim($clientName)));
    $base = '';

    if (count($words) >= 3) {
        // Use first letters of first 3 words (e.g., First National Bank → FNB)
        $base = substr($words[0], 0, 1) . substr($words[1], 0, 1) . substr($words[2], 0, 1);
    } elseif (strlen($clientName) >= 3) {
        // Use first 3 letters of name
        $base = strtoupper(substr(preg_replace('/[^A-Z]/i', '', $clientName), 0, 3));
    } else {
        // Pad with letters if shorter than 3 (e.g., IT → ITA)
        $base = strtoupper($clientName);
        $alphabet = range('A', 'Z');
        for ($i = 0; strlen($base) < 3 && $i < count($alphabet); $i++) {
            $base .= $alphabet[$i];
        }
    }

    // Get the last matching code and increment
    $stmt = $conn->prepare("SELECT client_code FROM clients WHERE client_code LIKE CONCAT(?, '%') ORDER BY client_code DESC LIMIT 1");
    $stmt->bind_param("s", $base);
    $stmt->execute();
    $stmt->bind_result($lastCode);
    $stmt->fetch();
    $stmt->close();

    $number = ($lastCode) ? ((int)substr($lastCode, 3) + 1) : 1;
    $suffix = str_pad($number, 3, '0', STR_PAD_LEFT);

    return $base . $suffix;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $type = trim($_POST['type']);

    if (empty($name)) {
        $message = "❌ Client name is required.";
    } else {
        $client_code = generateClientCode($conn, $name);
        $stmt = $conn->prepare("INSERT INTO clients (name, description, type, client_code) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $description, $type, $client_code);

        if ($stmt->execute()) {
            $client_id = $stmt->insert_id;
            $message = "✅ Client saved successfully! Client Code: $client_code";
        } else {
            $message = "❌ Failed to save client.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Add New Client</title>
<style>
  body {
    background: #dfe6e9;
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
}
.container {
    background: #ffffff;
    padding: 20px;
    max-width: 450px;
    width: 100%;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}
img.logo {
    max-height: 40px;
    display: block;
    margin: 0 auto 15px;
}
h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 20px;
}
.message {
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    font-size: 14px;
}
.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
label {
    font-weight: 600;
    display: block;
    margin-top: 10px;
}
input, textarea {
    width: 100%;
    padding: 8px;
    margin-top: 4px;
    border-radius: 4px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}
textarea { resize: vertical; min-height: 60px; }
button {
    background: #c0392b;
    color: white;
    border: none;
    padding: 10px 16px;
    margin-top: 15px;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
}
button:hover { background: #922b21; }
a.back-link {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: #c0392b;
    text-decoration: none;
}
a.back-link:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="container">
  <img src="image.png" alt="Logo" class="logo">
  <h2>Add New Client</h2>

  <?php if ($message): ?>
    <div class="message <?= str_starts_with($message, '✅') ? 'success' : 'error' ?>">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <form method="POST" onsubmit="return validateClientForm()">
    <label for="name">Name: *</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

    <label for="description">Description:</label>
    <textarea id="description" name="description"><?= htmlspecialchars($description) ?></textarea>

    <label for="type">Type:</label>
    <input type="text" id="type" name="type" value="<?= htmlspecialchars($type) ?>">

    <button type="submit">Save Client</button>
  </form>

  <a class="back-link" href="clients.php">← Back to Clients List</a>
</div>
<script>
  function validateClientForm() {
    const name = document.forms[0]["name"].value.trim();
    if (name === "") {
      alert("Client name is required.");
      return false;
    }
    return true;
  }
</script>
</body>
</html>
