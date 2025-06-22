<?php
include 'db.php';

// Fetch clients ordered by name
$result = $conn->query("SELECT * FROM clients ORDER BY name ASC");
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
    font-size: 14px;  /* smaller font */
  }
  /* Logo style */
  img.logo {
    max-height: 40px;
    float: right;
    margin-right: 12px;
  }
  h2 {
    color: #2c3e50;
    margin-bottom: 12px;
    font-size: 1.4rem; /* smaller heading */
  }
  a {
    text-decoration: none;
    color: #c0392b; /* changed from blue to red */
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
    font-size: 13px; /* smaller text inside table */
  }
  thead {
    background-color: #c0392b; /* changed from blue to red */
    color: white;
  }
  th, td {
    text-align: left;
    padding: 8px 10px; /* smaller padding */
  }
  tbody tr {
    border-bottom: 1px solid #ddd;
    transition: background-color 0.3s ease;
  }
  tbody tr:hover {
    background-color: #fbeaea; /* lighter red hover */
  }
  tbody tr:last-child {
    border-bottom: none;
  }
  td.center {
    text-align: center;
    font-weight: 600;
  }
  .btn-manage {
  background-color:rgb(249, 76, 56); /* light red */
  color: white;
  padding: 5px 12px;
  border-radius: 4px;
  font-size: 0.85rem;
  transition: background-color 0.2s ease;
  display: inline-block;
}
.btn-manage:hover {
  background-color: #c0392b; /* darker red on hover */
}

  .add-new {
    display: inline-block;
    margin-bottom: 12px;
    background-color: #c0392b; /* changed from green to red */
    color: white;
    padding: 6px 14px; /* smaller */
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.9rem;
  }
  .add-new:hover {
    background-color: #922b21; /* darker red */
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
  }
</style>
</head>
<body>

<!-- Logo at the top -->
<img src="image.png" alt="Logo" class="logo" />

<h2>Clients List</h2>
<a class="add-new" href="add_client.php">+ Add New Client</a>

<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Client Code</th>
      <th class="center">No. of linked contacts</th>
      <th>Manage</th>
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
    </tr>
  <?php endwhile; ?>
<?php else: ?>
  <tr><td colspan="4" style="text-align:center; padding: 20px; font-style: italic;">No client(s) found.</td></tr>
<?php endif; ?>
  </tbody>
</table>

</body>
</html>
