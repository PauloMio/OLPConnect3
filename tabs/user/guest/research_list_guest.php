<?php
include '../../../database/db_connect.php';

// Filtering & Searching
$filterCategory = isset($_GET['filter_category']) ? $_GET['filter_category'] : '';
$searchQuery    = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$sql = "SELECT * FROM research WHERE 1";
$params = [];
$types = "";

if ($filterCategory !== '') {
    $sql .= " AND category=?";
    $params[] = $filterCategory;
    $types .= "s";
}

if ($searchQuery !== '') {
    $sql .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
    $types .= "ss";
}

$sql .= " ORDER BY id DESC";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Fetch categories for dropdown
$categories = $conn->query("SELECT category FROM research_category ORDER BY category ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Research List Guest</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #f5f7fa;
}
.card {
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.table thead {
    background-color: #0d6efd;
    color: white;
}
.table-hover tbody tr:hover {
    background-color: #e9f0ff;
}
.form-select, .form-control {
    border-radius: 10px;
}
</style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Research List Guest</h2>
    
    <div class="card p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <form method="GET" id="filterForm">
                    <select name="filter_category" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Categories</option>
                        <?php while($c = $categories->fetch_assoc()): 
                            $selected = ($c['category'] == $filterCategory) ? "selected" : "";
                        ?>
                        <option value="<?= htmlspecialchars($c['category']) ?>" <?= $selected ?>><?= htmlspecialchars($c['category']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </form>
            </div>
            <div class="col-md-8">
                <form method="GET">
                    <input type="hidden" name="filter_category" value="<?= htmlspecialchars($filterCategory) ?>">
                    <input type="text" name="search" class="form-control" placeholder="Search by Title or Author" value="<?= htmlspecialchars($searchQuery) ?>" 
                           onkeypress="if(event.key==='Enter'){this.form.submit();}" id="searchBox">
                </form>
            </div>
        </div>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Year</th>
                        <th>Category</th>
                        <th>Program</th>
                        <th>Department</th>
                        <th>Accession No</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['author']) ?></td>
                        <td><?= htmlspecialchars($row['year']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= htmlspecialchars($row['program']) ?></td>
                        <td><?= htmlspecialchars($row['Department']) ?></td>
                        <td><?= htmlspecialchars($row['accession_no']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const searchBox = document.getElementById('searchBox');
searchBox.addEventListener('input', function() {
    if(this.value.trim() === '') {
        this.form.submit();
    }
});
</script>
</body>
</html>
