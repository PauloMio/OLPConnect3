<?php
include '../../../database/db_connect.php'; // fixed path

// Build search and category filter query
$where = [];
$params = [];
$types = "";

if (!empty($_GET['search'])) {
    $where[] = "(title LIKE ? OR author LIKE ?)";
    $search = "%" . $_GET['search'] . "%";
    $params[] = $search;
    $params[] = $search;
    $types .= "ss";
}

if (!empty($_GET['category_filter'])) {
    $where[] = "category = ?";
    $params[] = $_GET['category_filter'];
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
        .ebook-card img {
            object-fit: cover;
            height: 200px;
            width: 100%;
        }
        .ebook-card {
            transition: transform 0.2s;
        }
        .ebook-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-light p-4">
<div class="container">
    <h2 class="mb-4 text-center">📚 Ebook Collection</h2>

    <!-- Search & Category Filter -->
    <form method="get" class="row g-2 mb-4">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control"
                   placeholder="Search by Title or Author"
                   value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        </div>
        <div class="col-md-4">
            <select name="category_filter" class="form-select">
                <option value="">-- Filter by Category --</option>
                <?php $categories->data_seek(0); while($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['category'] ?>"
                        <?= (isset($_GET['category_filter']) && $_GET['category_filter'] == $cat['category']) ? 'selected' : '' ?>>
                        <?= $cat['category'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Apply</button>
        </div>
    </form>

    <div class="row g-4">
        <?php if ($ebooks->num_rows > 0): ?>
            <?php while ($row = $ebooks->fetch_assoc()): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card ebook-card shadow-sm h-100">
                        <?php if (!empty($row['coverage']) && file_exists("../../uploads/coverage/".$row['coverage'])): ?>
                            <img src="../../uploads/coverage/<?= $row['coverage'] ?>" class="card-img-top" alt="Cover Image">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x200?text=No+Cover" class="card-img-top" alt="No Cover">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                            <p class="card-text mb-1"><strong>Author:</strong> <?= htmlspecialchars($row['author']) ?></p>
                            <p class="card-text mb-1"><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
                            <p class="card-text mb-1"><strong>Year:</strong> <?= htmlspecialchars($row['copyrightyear']) ?></p>
                            <p class="card-text mb-3"><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                            <a href="ebook_details.php?id=<?= $row['id'] ?>" class="btn btn-primary mt-auto">Read More</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No ebooks available.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
