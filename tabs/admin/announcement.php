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

// ===== CREATE =====
if (isset($_POST['add_announcement'])) {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = time() . "_" . basename($_FILES['image']['name']);
        $uploadDir = __DIR__ . "/../uploads/announcement/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);

        $stmt = $conn->prepare("INSERT INTO announcements (image_path, created_at, updated_at) VALUES (?, NOW(), NOW())");
        $stmt->bind_param("s", $image);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// ===== DELETE =====
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("SELECT image_path FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result && file_exists(__DIR__ . "/../uploads/announcement/" . $result['image_path'])) {
        unlink(__DIR__ . "/../uploads/announcement/" . $result['image_path']);
    }

    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ===== READ =====
$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcement Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div id="main-content" style="padding:20px;">
    <div class="container my-5">
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Add Announcement</h4>
        </div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-8">
                    <input type="file" name="image" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="add_announcement" class="btn btn-success w-100">Add Announcement</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">All Announcements</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $announcements->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>
                            <img src="../uploads/announcement/<?= $row['image_path'] ?>" class="img-thumbnail" style="max-width: 150px;">
                        </td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this announcement?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($announcements->num_rows == 0): ?>
                    <tr>
                        <td colspan="4" class="text-center">No announcements found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
