<?php
ob_start();
session_start();

// Protect page (redirect if not logged in)
if (!isset($_SESSION['user_id'])) {
    header("Location: login_admin.php");
    exit;
}

// Include sidebar (it also includes db_connection.php)
include 'sidebar.php';

// Get search query if available
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// SQL with optional WHERE clause
if ($search !== "") {
    $sql = "SELECT * FROM guestlog WHERE name LIKE '%" . $conn->real_escape_string($search) . "%' ORDER BY created_at DESC";
} else {
    $sql = "SELECT * FROM guestlog ORDER BY created_at DESC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Guest Records</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div id="main-content" style="padding:20px;">
    <div class="container">
  <div class="card shadow-lg p-4 rounded-3">
    <h3 class="mb-4 text-center">Guest Records</h3>

    <!-- Search Bar -->
    <form method="GET" class="mb-3">
      <input 
        type="text" 
        name="search" 
        class="form-control" 
        placeholder="Search by name..." 
        value="<?= htmlspecialchars($search) ?>"
        oninput="if(this.value===''){ window.location='guest_record.php'; }"
      >
    </form>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>School</th>
            <th>ID Number</th>
            <th>Course</th>
            <th>Purpose</th>
            <th>Created At</th>
            <th>Updated At</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['name'])) ?></td>
                <td><?= nl2br(htmlspecialchars($row['school'])) ?></td>
                <td><?= htmlspecialchars($row['id_num']) ?></td>
                <td><?= htmlspecialchars($row['course']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['purpose'])) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td><?= htmlspecialchars($row['updated_at']) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center">No guest records found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>



</body>
</html>
