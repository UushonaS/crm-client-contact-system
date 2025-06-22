<?php
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$contact_id = isset($_GET['contact_id']) ? intval($_GET['contact_id']) : 0;

if ($contact_id <= 0) {
    echo "❌ Invalid contact ID.";
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selected_clients = isset($_POST['client_ids']) ? $_POST['client_ids'] : [];

    $conn->query("DELETE FROM client_contact_links WHERE contact_id = $contact_id");

    if (!empty($selected_clients)) {
        $stmt = $conn->prepare("INSERT INTO client_contact_links (client_id, contact_id) VALUES (?, ?)");
        foreach ($selected_clients as $client_id) {
            $stmt->bind_param("ii", $client_id, $contact_id);
            $stmt->execute();
        }
        $message = "✅ Clients linked successfully.";
    } else {
        $message = "✅ All client links removed.";
    }
}

$clients_result = $conn->query("SELECT id, name, client_code FROM clients ORDER BY name ASC");

$linked_clients_ids = [];
$check_links = $conn->prepare("SELECT client_id FROM client_contact_links WHERE contact_id = ?");
$check_links->bind_param("i", $contact_id);
$check_links->execute();
$res = $check_links->get_result();
while ($r = $res->fetch_assoc()) {
    $linked_clients_ids[] = $r['client_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Link Clients to Contact</title>
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
            margin-top: 20px;
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

        .message {
            margin: 10px 0;
            padding: 10px;
            background-color: #eafaf1;
            border-left: 5px solid #2ecc71;
            color: #2e7d32;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="top-header">
    <h2>Link Clients to Contact ID: <?= $contact_id ?></h2>
    <img src="image.png" alt="Logo" class="logo" />
</div>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST">
    <table>
        <thead>
            <tr>
                <th>Link</th>
                <th>Client Name</th>
                <th>Client Code</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($clients_result->num_rows > 0): ?>
            <?php while ($row = $clients_result->fetch_assoc()): ?>
                <?php
                    $client_id = $row['id'];
                    $linked = in_array($client_id, $linked_clients_ids);
                ?>
                <tr>
                    <td><input type="checkbox" name="client_ids[]" value="<?= $client_id ?>" <?= $linked ? "checked" : "" ?>></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['client_code']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">No client(s) found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <br>
    <button type="submit">Save Links</button>
</form>

<br>
<a href="add_contact.php?id=<?= $contact_id ?>">← Back to Contact Form</a>

</body>
</html>
