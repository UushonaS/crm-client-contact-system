<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Optionally check for admin role
if ($_SESSION['role'] !== 'admin') {
    // Redirect or show access denied for pages requiring admin
    // header("Location: unauthorized.php");
    // exit();
}

include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get message from URL (if any)
$message = '';
if (!empty($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Prepare SQL query with optional search filter
if ($search !== '') {
    $search_param = "%" . $search . "%";
    $stmt = $conn->prepare("SELECT * FROM clients WHERE name LIKE ? OR client_code LIKE ? ORDER BY name ASC");
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM clients ORDER BY name ASC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Clients List</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f9fafb;
    margin: 20px;
    color: #333;
    font-size: 14px;
  }
  img.logo {
    max-height: 40px;
    float: right;
    margin-right: 12px;
  }
  h2 {
    color: #2c3e50;
    margin-bottom: 12px;
    font-size: 1.4rem;
  }
  a {
    text-decoration: none;
    color: #c0392b;
    font-weight: 600;
    font-size: 0.9rem;
  }
  a:hover {
    text-decoration: underline;
  }
  table {
    border-collapse: collapse;
    width: 100%;
    background: #fff;
    box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
    border-radius: 5px;
    overflow: hidden;
    font-size: 13px;
  }
  thead {
    background-color: #c0392b;
    color: white;
  }
  th, td {
    text-align: left;
    padding: 8px 10px;
  }
  tbody tr {
    border-bottom: 1px solid #ddd;
    transition: background-color 0.3s ease;
  }
  tbody tr:hover {
    background-color: #fbeaea;
  }
  tbody tr:last-child {
    border-bottom: none;
  }
  td.center {
    text-align: center;
    font-weight: 600;
  }
  .btn-manage {
    background-color: rgb(249, 76, 56);
    color: white;
    padding: 5px 12px;
    border-radius: 4px;
    font-size: 0.85rem;
    transition: background-color 0.2s ease;
    display: inline-block;
    margin-right: 6px;
  }
  .btn-manage:hover {
    background-color: #c0392b;
  }
  .btn-delete {
    background-color: #e74c3c;
    color: white;
    padding: 5px 12px;
    border-radius: 4px;
    font-size: 0.85rem;
    transition: background-color 0.2s ease;
    display: inline-block;
  }
  .btn-delete:hover {
    background-color: #c0392b;
  }
  .add-new {
    display: inline-block;
    margin-bottom: 12px;
    background-color: #c0392b;
    color: white;
    padding: 6px 14px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.9rem;
  }
  .add-new:hover {
    background-color: #922b21;
  }
  .message {
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 5px;
    font-weight: 600;
  }
  .message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
  }
  .message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
  }
  .search-box {
    margin-bottom: 15px;
  }
  input[type="text"] {
    width: 300px;
    padding: 8px;
    font-size: 0.9rem;
    border-radius: 4px;
    border: 1px solid #ccc;
  }
  button.search-btn {
    padding: 8px 12px;
    font-size: 0.9rem;
    border-radius: 4px;
    border: none;
    background-color: #c0392b;
    color: white;
    cursor: pointer;
  }
  button.search-btn:hover {
    background-color: #922b21;
  }
  a.clear-link {
    margin-left: 10px;
    font-size: 0.9rem;
    color: #c0392b;
    text-decoration: none;
  }
  a.clear-link:hover {
    text-decoration: underline;
  }
  @media (max-width: 600px) {
    table, thead, tbody, th, td, tr {
      display: block;
    }
    thead tr {
      display: none;
    }
    tbody tr {
      margin-bottom: 12px;
      border-bottom: 2px solid #ddd;
    }
    tbody td {
      padding-left: 45%;
      position: relative;
      text-align: left;
      border: none;
      border-bottom: 1px solid #eee;
    }
    tbody td::before {
      position: absolute;
      top: 10px;
      left: 12px;
      width: 40%;
      white-space: nowrap;
      font-weight: 600;
      color: #555;
      font-size: 0.85rem;
    }
    tbody td:nth-of-type(1)::before { content: "Name"; }
    tbody td:nth-of-type(2)::before { content: "Client Code"; }
    tbody td:nth-of-type(3)::before { content: "No. of linked contacts"; }
    tbody td:nth-of-type(4)::before { content: "Manage"; }
    tbody td:nth-of-type(5)::before { content: "Delete"; }
  }
</style>
<script>
  function confirmDelete() {
    return confirm('Are you sure you want to delete this client?');
  }
</script>
</head>
<body>

<!-- Logo at the top -->
<img src="image.png" alt="Logo" class="logo" />

<h2>Clients List</h2>

<?php if ($message): ?>
  <div class="message <?= strpos($message, '❌') === 0 ? 'error' : 'success' ?>">
    <?= $message ?>
  </div>
<?php endif; ?>

<form method="GET" action="" class="search-box">
  <input type="text" name="search" placeholder="Search by name or client code" value="<?= htmlspecialchars($search) ?>" />
  <button type="submit" class="search-btn">Search</button>
  <?php if ($search !== ''): ?>
    <a href="clients.php" class="clear-link">Clear</a>
  <?php endif; ?>
</form>

<a class="add-new" href="add_client.php">+ Add New Client</a>

<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Client Code</th>
      <th class="center">No. of linked contacts</th>
      <th>Manage</th>
      <th>Delete</th>
    </tr>
  </thead>
  <tbody>
<?php if ($result->num_rows > 0): ?>
  <?php while ($row = $result->fetch_assoc()): ?>
    <?php
      $client_id = $row['id'];

      // Count linked contacts
      $countStmt = $conn->prepare("SELECT COUNT(*) FROM client_contact_links WHERE client_id = ?");
      $countStmt->bind_param("i", $client_id);
      $countStmt->execute();
      $countStmt->bind_result($linkedCount);
      $countStmt->fetch();
      $countStmt->close();
    ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['client_code']) ?></td>
      <td class="center"><?= $linkedCount ?></td>
      <td><a class="btn-manage" href="link_client_contacts.php?client_id=<?= $client_id ?>">Manage Contacts</a></td>
      <td>
        <a class="btn-delete" href="delete_client.php?client_id=<?= $client_id ?>" onclick="return confirmDelete()">Delete</a>
      </td>
    </tr>
  <?php endwhile; ?>
<?php else: ?>
  <tr><td colspan="5" style="text-align:center; padding: 20px; font-style: italic;">No client(s) found.</td></tr>
<?php endif; ?>
  </tbody>
</table>

</body>
</html>
