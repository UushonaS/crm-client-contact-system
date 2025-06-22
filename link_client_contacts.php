<?php
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get client ID from URL
$client_id = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = (int)$_POST['client_id'];
    $selected_contacts = $_POST['contacts'] ?? [];

    $conn->query("DELETE FROM client_contact_links WHERE client_id = $client_id");

    $stmt = $conn->prepare("INSERT INTO client_contact_links (client_id, contact_id) VALUES (?, ?)");
    foreach ($selected_contacts as $contact_id) {
        $contact_id = (int)$contact_id;
        $stmt->bind_param("ii", $client_id, $contact_id);
        $stmt->execute();
    }

    echo "<p style='color: green;'>✅ Links updated successfully!</p>";
}

// Fetch client info
$client = null;
if ($client_id > 0) {
    $stmt = $conn->prepare("SELECT name FROM clients WHERE id = ?");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $client = $result->fetch_assoc();
}

// Fetch all contacts
$contacts_result = $conn->query("SELECT id, name, surname FROM contacts ORDER BY surname, name ASC");
if (!$contacts_result) {
    die("Failed to fetch contacts: " . $conn->error);
}

// Fetch linked contacts
$linked_contacts = [];
if ($client_id > 0) {
    $stmt = $conn->prepare("SELECT contact_id FROM client_contact_links WHERE client_id = ?");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $linked_contacts[] = $row['contact_id'];
    }
}
$contacts_result->data_seek(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Link Contacts to Client</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9fafb;
            margin: 20px;
            color: #333;
        }

        img.logo {
            max-height: 40px;
            float: right;
            margin-left: 12px;
        }

        h2 {
            color: #c0392b;
            margin-bottom: 20px;
            line-height: 40px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            font-size: 14px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        thead {
            background-color: #c0392b;
            color: #fff;
        }

        td.center {
            text-align: center;
        }

        button {
            background-color: #c0392b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background-color: #922b21;
        }

        a {
            color: #c0392b;
            font-weight: 600;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

<div class="top-header">
    <h2>Link Contacts to Client: <?= htmlspecialchars($client['name']) ?></h2>
    <img src="image.png" alt="Logo" class="logo" />
</div>

<form method="POST" action="">
    <input type="hidden" name="client_id" value="<?= $client_id ?>">

    <table>
        <thead>
            <tr>
                <th>Contact Name</th>
                <th class="center">Linked</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($contact = $contacts_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($contact['surname'] . ' ' . $contact['name']) ?></td>
                <td class="center">
                    <input type="checkbox" name="contacts[]" value="<?= $contact['id'] ?>"
                    <?= in_array($contact['id'], $linked_contacts) ? 'checked' : '' ?>>
                </td>
                <td>
                    <?php if (in_array($contact['id'], $linked_contacts)): ?>
                        <a href="unlink_link.php?client_id=<?= $client_id ?>&contact_id=<?= $contact['id'] ?>&from=client">Unlink</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <br>
    <button type="submit">Save Links</button>
</form>

<br>
<a href="clients.php">← Back to Clients List</a>

</body>
</html>
