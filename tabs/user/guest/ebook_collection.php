<?php
include '../../../database/db_connect.php'; // fixed path

// Search & Category filter
$searchQuery = $_GET['search'] ?? '';
$categoryFilter = $_GET['category_filter'] ?? '';

// Build query
$where = [];
$params = [];
$types = "";

if (!empty($searchQuery)) {
    $where[] = "(title LIKE ? OR author LIKE ?)";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
    $types .= "ss";
}

if (!empty($categoryFilter)) {
    $where[] = "category = ?";
    $params[] = $categoryFilter;
    $types .= "s";
}

$sql = "SELECT * FROM ebooks";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$ebooks = $stmt->get_result();

// Fetch categories
$categories = $conn->query("SELECT * FROM ebook_category");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Guest Ebook Collection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .ebook-card { transition: transform 0.2s; }
        .ebook-card:hover { transform: translateY(-5px); }
        .cover-wrapper { width: 100%; height: 350px; display: flex; justify-content: center; align-items: center; background: #f8f9fa; overflow: hidden; border-bottom: 1px solid #ddd; }
        .cover-wrapper img { height: 100%; width: auto; object-fit: cover; }
        .card-body { display: flex; flex-direction: column; }
        .card-body .btn { margin-top: auto; }
    </style>
</head>
<body class="bg-light p-0">

<!-- 🌐 Top Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">📚 Digital Library</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'ebook_collection.php' ? 'active' : '' ?>" href="ebook_collection.php">Ebook Collection</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'research_list_guest.php' ? 'active' : '' ?>" href="research_list_guest.php">Research List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="/index.php">🚪 Leave</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<!-- 🧱 Main Container -->
<div class="container p-4">
    <h2 class="mb-4 text-center">📚 Ebook Collection</h2>

    <!-- Search & Category Filter -->
    <form method="get" class="row g-2 mb-4" id="filterForm">
        <div class="col-md-6">
            <input type="text" name="search" id="searchBox" class="form-control"
                   placeholder="Search by Title or Author"
                   value="<?= htmlspecialchars($searchQuery) ?>"
                   onkeypress="if(event.key==='Enter'){this.form.submit();}">
        </div>
        <div class="col-md-4">
            <select name="category_filter" id="categoryFilter" class="form-select" onchange="this.form.submit()">
                <option value="">-- Filter by Category --</option>
                <?php $categories->data_seek(0); while($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($cat['category']) ?>" 
                        <?= ($categoryFilter == $cat['category']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </form>

    <!-- Ebook Grid -->
    <div class="row g-4">
        <?php if ($ebooks->num_rows > 0): ?>
            <?php while ($row = $ebooks->fetch_assoc()): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card ebook-card shadow-sm h-100">
                        <div class="cover-wrapper">
                            <?php if (!empty($row['coverage']) && file_exists("../../uploads/coverage/".$row['coverage'])): ?>
                                <img src="../../uploads/coverage/<?= $row['coverage'] ?>" alt="Cover Image">
                            <?php else: ?>
                                <img src="../../../images/icons/defaultcover.png" alt="No Cover">
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                            <p class="card-text mb-1"><strong>Author:</strong> <?= htmlspecialchars($row['author']) ?></p>
                            <p class="card-text mb-1"><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
                            <p class="card-text mb-1"><strong>Year:</strong> <?= htmlspecialchars($row['copyrightyear']) ?></p>
                            <p class="card-text mb-3"><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                            <a href="ebook_details.php?id=<?= $row['id'] ?>" class="btn btn-primary">Read More</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No ebooks available.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Reload all ebooks if search box is cleared
    document.getElementById("searchBox").addEventListener("input", function() {
        if (this.value.trim() === "") {
            document.getElementById("filterForm").submit();
        }
    });
</script>
</body>
</html>
