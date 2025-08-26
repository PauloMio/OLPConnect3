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
        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar space control */
        #main-content {
            transition: margin-left 0.3s ease;
            flex: 1;
        }

        /* Open sidebar */
        .sidebar-open #main-content {
            margin-left: 220px; /* width of sidebar */
        }

        /* Closed sidebar */
        .sidebar-closed #main-content {
            margin-left: 70px; /* collapsed sidebar width */
        }

        .ebook-card {
            transition: transform 0.2s;
        }
        .ebook-card:hover {
            transform: translateY(-5px);
        }

        /* Portrait cover wrapper */
        .cover-wrapper {
            width: 100%;
            height: 350px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8f9fa;
            overflow: hidden;
            border-bottom: 1px solid #ddd;
        }
        .cover-wrapper img {
            height: 100%;
            width: auto;
            object-fit: cover;
        }

        .card-body {
            display: flex;
            flex-direction: column;
        }
        .card-body .btn {
            margin-top: auto;
        }
    </style>
</head>
<body class="sidebar-open">

<body class="bg-light">
<?php include 'sidebar.php'; ?>

<div class="content" id="main-content">
    <div class="container p-4">
        <h2 class="mb-4 text-center">📚 Ebook Collection</h2>
        
	<!-- Search & Category Filter -->
        <form method="get" class="row g-2 mb-4">
            <div class="col-md-6">
                <input type="text" name="search" id="searchBox" class="form-control"
                       placeholder="Search by Title or Author"
                       value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            </div>
            <div class="col-md-4">
                <select name="category_filter" id="categoryFilter" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Filter by Category --</option>
                    <?php $categories->data_seek(0); while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['category'] ?>"
                            <?= (isset($_GET['category_filter']) && $_GET['category_filter'] == $cat['category']) ? 'selected' : '' ?>>
                            <?= $cat['category'] ?>
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
// Auto-search with debounce to keep cursor in place
const searchBox = document.getElementById("searchBox");
let debounceTimer;

searchBox.addEventListener("input", function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        // Only submit after user stops typing for 500ms
        this.form.submit();
    }, 500); // Adjust delay as needed
});
</script>
</body>
</html>
