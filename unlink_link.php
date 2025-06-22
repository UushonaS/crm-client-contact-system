<?php
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$contact_id = isset($_GET['contact_id']) ? (int)$_GET['contact_id'] : 0;
$client_id = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;
$from = isset($_GET['from']) ? $_GET['from'] : '';

if ($contact_id > 0 && $client_id > 0) {
    $stmt = $conn->prepare("DELETE FROM client_contact_links WHERE contact_id = ? AND client_id = ?");
    $stmt->bind_param("ii", $contact_id, $client_id);
    $stmt->execute();

    // Redirect back to linking page
    if ($from === 'contact') {
        header("Location: link_contact_clients.php?contact_id=$contact_id");
    } elseif ($from === 'client') {
        header("Location: link_client_contacts.php?client_id=$client_id");
    } else {
        header("Location: contacts.php");
    }
    exit;
} else {
    echo "Invalid request.";
}
