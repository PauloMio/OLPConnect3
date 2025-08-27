<?php
include '../../database/db_connect.php';

// Fetch programs for dropdown
$programs = [];
$res = $conn->query("SELECT program FROM program_user ORDER BY program ASC");
while ($row = $res->fetch_assoc()) {
    $programs[] = $row['program'];
}

// Handle search
$search = "";
$where  = "";
if (!empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $like = "%" . $conn->real_escape_string($search) . "%";
    $where = "WHERE firstname LIKE '$like' OR lastname LIKE '$like' OR schoolid LIKE '$like'";
}

// CREATE
if (isset($_POST['create'])) {
    $firstname = $_POST['firstname'];
    $lastname  = $_POST['lastname'];
    $schoolid  = $_POST['schoolid'];
    $program   = $_POST['program'];
    $birthdate = $_POST['birthdate'];

    $status = "inactive"; // default

    $stmt = $conn->prepare("INSERT INTO account (firstname, lastname, schoolid, program, birthdate, status, created_at, updated_at) 
                            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssssss", $firstname, $lastname, $schoolid, $program, $birthdate, $status);
    $stmt->execute();
    $stmt->close();
    header("Location: accounts.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id       = $_POST['id'];
    $firstname= $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $schoolid = $_POST['schoolid'];
    $program  = $_POST['program'];
    $birthdate= $_POST['birthdate'];

    $stmt = $conn->prepare("UPDATE account SET firstname=?, lastname=?, schoolid=?, program=?, birthdate=?, updated_at=NOW() WHERE id=?");
    $stmt->bind_param("sssssi", $firstname, $lastname, $schoolid, $program, $birthdate, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: accounts.php");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM account WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: accounts.php");
    exit;
}

// FETCH accounts (with optional search)
$sql = "SELECT * FROM account $where ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accounts Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="p-4">

    <h2 class="mb-4">Account Management</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" class="d-flex" style="max-width:300px;">
            <input type="text" name="search" id="searchBox" class="form-control"
                   placeholder="Search by Name or ID"
                   value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>

    <!-- Add + Search row -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
          + Add Account
        </button>
    </div>

    <!-- Accounts Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Full Name</th>
                <th>School ID</th>
                <th>Program</th>
                <th>Status</th>
                <th width="150">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']) ?></td>
                    <td><?= htmlspecialchars($row['schoolid']) ?></td>
                    <td><?= htmlspecialchars($row['program']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <!-- Edit button -->
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                        <a href="accounts.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this account?')" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <form method="POST">
                        <div class="modal-header">
                          <h5 class="modal-title">Edit Account</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id" value="<?= $row['id'] ?>">

                          <div class="row mb-3">
                            <div class="col">
                              <label>First Name</label>
                              <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($row['firstname']) ?>" required>
                            </div>
                            <div class="col">
                              <label>Last Name</label>
                              <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($row['lastname']) ?>" required>
                            </div>
                          </div>

                          <div class="row mb-3">
                            <div class="col">
                              <label>School ID</label>
                              <input type="text" name="schoolid" class="form-control" value="<?= htmlspecialchars($row['schoolid']) ?>">
                            </div>
                            <div class="col">
                              <label>Program</label>
                              <select name="program" class="form-control" required>
                                  <option value="">-- Select Program --</option>
                                  <?php foreach ($programs as $prog): ?>
                                      <option value="<?= htmlspecialchars($prog) ?>" <?= $prog==$row['program']?'selected':'' ?>>
                                          <?= htmlspecialchars($prog) ?>
                                      </option>
                                  <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <div class="mb-3">
                            <label>Birthdate</label>
                            <input type="date" name="birthdate" class="form-control" value="<?= htmlspecialchars($row['birthdate']) ?>">
                          </div>

                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="update" class="btn btn-success">Save Changes</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">No accounts found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="POST">
            <div class="modal-header">
              <h5 class="modal-title">Add Account</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

              <div class="row mb-3">
                <div class="col">
                  <label>First Name</label>
                  <input type="text" name="firstname" class="form-control" required>
                </div>
                <div class="col">
                  <label>Last Name</label>
                  <input type="text" name="lastname" class="form-control" required>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col">
                  <label>School ID</label>
                  <input type="text" name="schoolid" class="form-control">
                </div>
                <div class="col">
                  <label>Program</label>
                  <select name="program" class="form-control" required>
                      <option value="">-- Select Program --</option>
                      <?php foreach ($programs as $prog): ?>
                          <option value="<?= htmlspecialchars($prog) ?>"><?= htmlspecialchars($prog) ?></option>
                      <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="mb-3">
                <label>Birthdate</label>
                <input type="date" name="birthdate" class="form-control">
              </div>

            </div>
            <div class="modal-footer">
              <button type="submit" name="create" class="btn btn-primary">Create</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>

<script>
// Press Enter to submit search
document.getElementById("searchBox").addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
        this.form.submit();
    }
});

// Clear search auto reload
document.getElementById("searchBox").addEventListener("input", function() {
    if (this.value === "") {
        window.location.href = "accounts.php";
    }
});
</script>

</body>
</html>
