<?php
include 'db.php';

// Fetch contacts ordered by surname, name
$result = $conn->query("SELECT * FROM contacts ORDER BY surname ASC, name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Contacts List</title>
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
    line-height: 40px;
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
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
    background-color: #f95438;
    color: white;
    padding: 5px 12px;
    border-radius: 4px;
    font-size: 0.85rem;
    transition: background-color 0.2s ease;
    display: inline-block;
  }

  .btn-manage:hover {
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
    tbody td:nth-of-type(2)::before { content: "Surname"; }
    tbody td:nth-of-type(3)::before { content: "Email"; }
    tbody td:nth-of-type(4)::before { content: "Linked Clients"; }
    tbody td:nth-of-type(5)::before { content: "Manage"; }
    tbody td.center {
      text-align: center;
      padding-left: 0;
    }
  }
</style>
</head>
<body>

<!-- Logo and Heading -->
<img src="image.png" alt="Logo" class="logo" />
<h2>Contacts List</h2>
<div style="clear: both;"></div>

<a class="add-new" href="add_contact.php">+ Add New Contact</a>

<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Surname</th>
      <th>Email</th>
      <th class="center">No. of linked clients</th>
      <th>Manage</th>
    </tr>
  </thead>
  <tbody>
<?php if ($result->num_rows > 0): ?>
  <?php while ($row = $result->fetch_assoc()): ?>
    <?php
      $contact_id = $row['id'];
      $countStmt = $conn->prepare("SELECT COUNT(*) FROM client_contact_links WHERE contact_id = ?");
      $countStmt->bind_param("i", $contact_id);
      $countStmt->execute();
      $countStmt->bind_result($linkedCount);
      $countStmt->fetch();
      $countStmt->close();
    ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['surname']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td class="center"><?= $linkedCount ?></td>
      <td><a class="btn-manage" href="link_contact_clients.php?contact_id=<?= $contact_id ?>">Manage Clients</a></td>
    </tr>
  <?php endwhile; ?>
<?php else: ?>
  <tr><td colspan="5" style="text-align:center; padding: 20px; font-style: italic;">No contact(s) found.</td></tr>
<?php endif; ?>
  </tbody>
</table>

</body>
</html>
