<?php
session_start();
include '../../../database/db_connect.php';

// Get logged-in account
$account = $_SESSION['account'] ?? null;

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
<title>Research List</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Layout */
body { display: flex; flex-direction: column; min-height: 100vh; overflow-x: hidden; font-family: Arial, sans-serif; background: #f0f2f5; }
#main-content { transition: margin-left 0.3s ease; flex-grow: 1; padding: 20px; }
.sidebar-open #main-content { margin-left: 240px; }
.sidebar-closed #main-content { margin-left: 70px; }

/* Sidebar styles (existing) */
.sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 240px; background-color: #2c3e50; color: white; display: flex; flex-direction: column; transition: width 0.3s ease; z-index: 1000; overflow: hidden; }
.sidebar.collapsed { width: 70px; }
.sidebar.collapsed .account-info { display: none; }

.toggle-btn { background: #1a252f; border: none; color: white; font-size: 22px; width: 100%; height: 40px; cursor: pointer; display: flex; align-items: center; justify-content: center; border-radius: 0; margin: 0; }
.toggle-btn:hover { background: #34495e; }

.menu { list-style: none; padding: 60px 0 0 0; margin: 0; flex-grow: 1; }
.menu li { margin: 0; }
.menu a { display: flex; align-items: center; padding: 0.75rem 1rem; text-decoration: none; color: white; transition: background 0.2s ease; white-space: nowrap; }
.menu a:hover { background-color: #34495e; }

.icon { width: 24px; height: 24px; filter: brightness(0) invert(1); flex-shrink: 0; }
.label { margin-left: 1rem; transition: opacity 0.2s ease, margin 0.2s ease; opacity: 1; }
.sidebar.collapsed .label { opacity: 0; margin-left: -9999px; }

.logout-section { padding: 1rem; border-top: 1px solid #34495e; }
.logout-btn { width: 100%; background-color: #dc3545; border: none; padding: 8px 0; border-radius: 4px; color: white; cursor: pointer; transition: background-color 0.2s; }
.logout-btn:hover { background-color: #b02a37; }

/* Main content styling */
.card { border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
.table thead { background-color: #0d6efd; color: white; }
.table-hover tbody tr:hover { background-color: #e9f0ff; }
.form-select, .form-control { border-radius: 10px; }

/* Footer */
footer { mt-auto; background: #f8f9fa; padding: 1rem 0; text-align: center; border-top: 1px solid #ddd; }
</style>
</head>
<body class="sidebar-open d-flex flex-column min-vh-100">

<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<div id="main-content" class="flex-grow-1">
    <h2 class="mb-4 text-center">Research List</h2>
    
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



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const searchBox = document.getElementById('searchBox');
searchBox.addEventListener('input', function() {
    if(this.value.trim() === '') {
        this.form.submit();
    }
});

// Sidebar toggle
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const body = document.body;

    toggleBtn.addEventListener('click', () => {
        if(body.classList.contains('sidebar-open')){
            body.classList.remove('sidebar-open');
            body.classList.add('sidebar-closed');
        } else {
            body.classList.remove('sidebar-closed');
            body.classList.add('sidebar-open');
        }
    });
});
</script>
</body>
</html>
