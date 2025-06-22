<?php
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['contact_id']) || !is_numeric($_GET['contact_id'])) {
    header("Location: contacts.php?msg=" . urlencode("❌ Invalid contact ID."));
    exit;
}

$contact_id = (int)$_GET['contact_id'];

// Check how many clients linked to this contact
$stmt = $conn->prepare("SELECT COUNT(*) FROM client_contact_links WHERE contact_id = ?");
$stmt->bind_param("i", $contact_id);
$stmt->execute();
$stmt->bind_result($linkedCount);
$stmt->fetch();
$stmt->close();

if ($linkedCount > 0) {
    // Cannot delete contact linked to clients
    $msg = "❌ Cannot delete. Contact is linked to {$linkedCount} client(s).";
    header("Location: contacts.php?msg=" . urlencode($msg));
    exit;
}

// Delete contact
$stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
$stmt->bind_param("i", $contact_id);
if ($stmt->execute()) {
    $msg = "✅ Contact deleted successfully.";
} else {
    $msg = "❌ Failed to delete contact.";
}
$stmt->close();

header("Location: contacts.php?msg=" . urlencode($msg));
exit;
