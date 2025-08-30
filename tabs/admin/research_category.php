<?php
include '../../database/db_connect.php';

// ===== CREATE =====
if (isset($_POST['add_category'])) {
    $category = trim($_POST['category']);
    if (!empty($category)) {
        $stmt = $conn->prepare("INSERT INTO research_category (category, created_at, updated_at) VALUES (?, NOW(), NOW())");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// ===== DELETE =====
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM research_category WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ===== READ =====
$categories = $conn->query("SELECT * FROM research_category ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Categories Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <!-- Add Research Category Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Add Research Category</h4>
        </div>
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="category" class="form-control" placeholder="Category Name" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="add_category" class="btn btn-success w-100">Add Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- List Research Categories Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">All Research Categories</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $categories->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($categories->num_rows == 0): ?>
                    <tr>
                        <td colspan="4" class="text-center">No categories found.</td>
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
