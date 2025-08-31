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

// Handle Create
if(isset($_POST['create_user'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $status = 'inactive'; // default status

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $status);
    $stmt->execute();
    $stmt->close();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Handle Update
if(isset($_POST['update_user'])){
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $email, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $email, $id);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Handle Delete
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Fetch Users
$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.password-toggle {
    position: relative;
}
.password-toggle .toggle-icon {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
}
</style>
</head>
<body>

<div id="main-content" style="padding:20px;">
    <div class="container mt-5">
    <h2 class="mb-4">Users Management</h2>

    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createModal">Add User</button>

    <!-- Search Box -->
    <div class="mb-3">
        <input type="text" id="searchBox" class="form-control" placeholder="Search by username or email...">
    </div>

    <table class="table table-bordered" id="usersTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= $row['status'] ?></td>
                <td>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($row['username']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>" required>
                        </div>
                        <div class="mb-3 password-toggle">
                            <label>Password (leave blank to keep current)</label>
                            <input type="password" name="password" class="form-control" id="editPassword<?= $row['id'] ?>">
                            <span class="toggle-icon" onclick="togglePassword('editPassword<?= $row['id'] ?>')">👁️</span>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" name="update_user" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>
                    </div>
                </form>
              </div>
            </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3 password-toggle">
                <label>Password</label>
                <input type="password" name="password" class="form-control" id="createPassword">
                <span class="toggle-icon" onclick="togglePassword('createPassword')">👁️</span>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" name="create_user" class="btn btn-success">Create</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
    </form>
  </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword(id){
    const input = document.getElementById(id);
    if(input.type === "password"){
        input.type = "text";
    } else {
        input.type = "password";
    }
}

// Search functionality
const searchBox = document.getElementById('searchBox');
const table = document.getElementById('usersTable').getElementsByTagName('tbody')[0];

searchBox.addEventListener('keyup', function(e) {
    // Trigger only on Enter key or if box is empty
    if (e.key === "Enter" || this.value === "") {
        const filter = this.value.toLowerCase();
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const username = rows[i].cells[1].textContent.toLowerCase();
            const email = rows[i].cells[2].textContent.toLowerCase();
            
            if (username.includes(filter) || email.includes(filter) || filter === "") {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
});
</script>
</body>
</html>
