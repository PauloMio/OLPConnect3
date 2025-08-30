<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../../../database/db_connect.php';

// Get logged-in account
$account_id = $_SESSION['account']['id'] ?? null;

// Fetch all favorite ebook IDs for this user
$fav_ebooks = [];
if ($account_id) {
    $res = $conn->query("SELECT ebook_id FROM account_ebook_favorite WHERE account_id = $account_id");
    while ($row = $res->fetch_assoc()) {
        $fav_ebooks[] = $row['ebook_id'];
    }
}

// Build search and category filter query
$where = [];
$params = [];
$types = "";

$searchQuery = $_GET['search'] ?? '';
$categoryFilter = $_GET['category_filter'] ?? '';

if (!empty($searchQuery)) {
    $where[] = "(title LIKE ? OR author LIKE ?)";
    $params[] = "%" . $searchQuery . "%";
    $params[] = "%" . $searchQuery . "%";
    $types .= "ss";
}

if (!empty($categoryFilter)) {
    $where[] = "category = ?";
    $params[] = $categoryFilter;
    $types .= "s";
}

// Build final SQL
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

// Fetch categories for dropdown
$categories = $conn->query("SELECT * FROM ebook_category");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Ebook Collection</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { display: flex; min-height: 100vh; overflow-x: hidden; }
#main-content { transition: margin-left 0.3s ease; flex: 1; }
.sidebar-open #main-content { margin-left: 220px; }
.sidebar-closed #main-content { margin-left: 70px; }

.ebook-card { transition: transform 0.2s; }
.ebook-card:hover { transform: translateY(-5px); }
.cover-wrapper { width: 100%; height: 350px; display: flex; justify-content: center; align-items: center; background: #f8f9fa; overflow: hidden; border-bottom:1px solid #ddd; }
.cover-wrapper img { height: 100%; width:auto; object-fit: cover; }

.card-body { display: flex; flex-direction: column; position: relative; }
.card-body .btn { margin-top: auto; }

.favorite-btn { position: absolute; top: 0.5rem; right: 0.5rem; font-size:1.5rem; border:none; background:none; cursor:pointer; }
</style>
</head>
<body class="sidebar-open">

<?php include 'sidebar.php'; ?>

<div class="content" id="main-content">
    <div class="container p-4">
        <h2 class="mb-4 text-center">📚 Ebook Collection</h2>

        <!-- Search & Category Filter -->
        <form method="get" class="row g-2 mb-4">
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

                            <?php if ($account_id): ?>
                                <button class="favorite-btn" data-ebook-id="<?= $row['id'] ?>">
                                    <?php if (in_array($row['id'], $fav_ebooks)): ?>
                                        <span style="color: gold;">★</span>
                                    <?php else: ?>
                                        <span style="color: gray;">☆</span>
                                    <?php endif; ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">No ebooks available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Optional: Auto-submit when textbox cleared (like reference)
document.getElementById("searchBox").addEventListener("input", function() {
    if(this.value.trim() === "") this.form.submit();
});

// Favorite toggle AJAX
document.querySelectorAll(".favorite-btn").forEach(btn => {
    btn.addEventListener("click", function() {
        const ebookId = this.dataset.ebookId;
        const span = this.querySelector("span");

        fetch("toggle_favorite.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "ebook_id=" + encodeURIComponent(ebookId)
        })
        .then(res => res.text())
        .then(res => {
            if(res.trim() === "added") {
                span.textContent = "★";
                span.style.color = "gold";
            } else {
                span.textContent = "☆";
                span.style.color = "gray";
            }
        })
        .catch(err => console.error(err));
    });
});
</script>
</body>
</html>
