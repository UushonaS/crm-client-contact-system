<?php
include 'db.php';

if (!isset($_GET['client_id']) || !is_numeric($_GET['client_id'])) {
    header("Location: clients.php?msg=" . urlencode("❌ Invalid client ID."));
    exit;
}

$client_id = (int) $_GET['client_id'];

// Check linked contacts count
$stmt = $conn->prepare("SELECT COUNT(*) FROM client_contact_links WHERE client_id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$stmt->bind_result($linkedCount);
$stmt->fetch();
$stmt->close();

if ($linkedCount > 0) {
    $msg = "❌ Cannot delete. Client is linked to {$linkedCount} contact(s).";
    header("Location: clients.php?msg=" . urlencode($msg));
    exit;
}

// Delete client
$stmt = $conn->prepare("DELETE FROM clients WHERE id = ?");
$stmt->bind_param("i", $client_id);
if ($stmt->execute()) {
    $msg = "✅ Client deleted successfully.";
} else {
    $msg = "❌ Failed to delete client.";
}
$stmt->close();

// Redirect back to clients page with message
header("Location: clients.php?msg=" . urlencode($msg));
exit;
