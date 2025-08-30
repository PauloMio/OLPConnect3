<?php
include '../../database/db_connect.php';

// ===== CREATE =====
if (isset($_POST['add_program'])) {
    $program = trim($_POST['program']);
    if (!empty($program)) {
        $stmt = $conn->prepare("INSERT INTO program_user (program, created_at, updated_at) VALUES (?, NOW(), NOW())");
        $stmt->bind_param("s", $program);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// ===== DELETE =====
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM program_user WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ===== READ =====
$programs = $conn->query("SELECT * FROM program_user ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Users Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <!-- Add Program Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Add Program</h4>
        </div>
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="program" class="form-control" placeholder="Program Name" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="add_program" class="btn btn-success w-100">Add Program</button>
                </div>
            </form>
        </div>
    </div>

    <!-- List Programs Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">All Programs</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Program</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $programs->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['program']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this program?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($programs->num_rows == 0): ?>
                    <tr>
                        <td colspan="4" class="text-center">No programs found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
