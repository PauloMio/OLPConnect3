<?php
include '../../database/db_connect.php';

// ===== CREATE =====
if (isset($_POST['add_ebook'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $edition = $_POST['edition'];
    $publisher = $_POST['publisher'];
    $copyrightyear = $_POST['copyrightyear'];
    $class = $_POST['class'];
    $subject = $_POST['subject'];
    $doi = $_POST['doi'];
    $category = $_POST['category'];
    $location = $_POST['location'];

    // Upload files with unique names
    $coverage = time() . "_" . basename($_FILES['coverage']['name']);
    $pdf = time() . "_" . basename($_FILES['pdf']['name']);

    move_uploaded_file($_FILES['coverage']['tmp_name'], __DIR__ . "/../uploads/coverage/$coverage");
    move_uploaded_file($_FILES['pdf']['tmp_name'], __DIR__ . "/../uploads/ebooks/$pdf");

    $stmt = $conn->prepare("INSERT INTO ebooks 
        (title, author, description, edition, publisher, copyrightyear, class, subject, doi, 
        category, location, coverage, pdf, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmt->bind_param("sssssisssssss", 
        $title, $author, $description, $edition, $publisher, $copyrightyear, 
        $class, $subject, $doi, $category, $location, $coverage, $pdf
    );
    $stmt->execute();
    header("Location: ebooks.php");
    exit;
}

// ===== UPDATE =====
if (isset($_POST['update_ebook'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $edition = $_POST['edition'];
    $publisher = $_POST['publisher'];
    $copyrightyear = $_POST['copyrightyear'];
    $class = $_POST['class'];
    $subject = $_POST['subject'];
    $doi = $_POST['doi'];
    $category = $_POST['category'];
    $location = $_POST['location'];

    // Handle file updates
    if (!empty($_FILES['coverage']['name'])) {
        $coverage = time() . "_" . basename($_FILES['coverage']['name']);
        move_uploaded_file($_FILES['coverage']['tmp_name'], __DIR__ . "/../uploads/coverage/$coverage");
    } else {
        $coverage = $_POST['old_coverage'];
    }

    if (!empty($_FILES['pdf']['name'])) {
        $pdf = time() . "_" . basename($_FILES['pdf']['name']);
        move_uploaded_file($_FILES['pdf']['tmp_name'], __DIR__ . "/../uploads/ebooks/$pdf");
    } else {
        $pdf = $_POST['old_pdf'];
    }

    $stmt = $conn->prepare("UPDATE ebooks SET 
        title=?, author=?, description=?, edition=?, publisher=?, copyrightyear=?, 
        class=?, subject=?, doi=?, category=?, location=?, coverage=?, pdf=?, updated_at=NOW() 
        WHERE id=?");

    $stmt->bind_param("sssssisssssssi", 
        $title, $author, $description, $edition, $publisher, $copyrightyear, 
        $class, $subject, $doi, $category, $location, $coverage, $pdf, $id
    );
    $stmt->execute();
    header("Location: ebooks.php");
    exit;
}

// ===== DELETE =====
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM ebooks WHERE id=$id");
    header("Location: ebooks.php");
}

// ===== FETCH =====
// $ebooks = $conn->query("SELECT * FROM ebooks");

$where = [];
$params = [];
$types = "";

// Search filter (title or author)
if (!empty($_GET['search'])) {
    $where[] = "(title LIKE ? OR author LIKE ?)";
    $search = "%" . $_GET['search'] . "%";
    $params[] = $search;
    $params[] = $search;
    $types .= "ss";
}

// Category filter
if (!empty($_GET['category_filter'])) {
    $where[] = "category = ?";
    $params[] = $_GET['category_filter'];
    $types .= "s";
}

// Sorting (latest-oldest or oldest-latest)
$order = "DESC"; // default
if (isset($_GET['sort']) && strtolower($_GET['sort']) === "asc") {
    $order = "ASC";
}

// Build final query
$sql = "SELECT * FROM ebooks";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id $order";

// Prepare and execute
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$ebooks = $stmt->get_result();

// for dropdowns
$categories = $conn->query("SELECT * FROM ebook_category");
$locations = $conn->query("SELECT * FROM ebook_location");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ebooks Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
    <h2 class="mb-4">📚 Ebooks Management</h2>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Ebook</button>

    <div class="card shadow-sm">
        <div class="card-body">

<form method="get" class="row g-2 mb-3">
    <div class="col-md-3">
        <input type="text" name="search" class="form-control"
               placeholder="Search by Title or Author"
               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
    </div>
    <div class="col-md-3">
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
    <div class="col-md-3">
        <select name="sort" class="form-select">
            <option value="desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'desc') ? 'selected' : '' ?>>
                Latest to Oldest
            </option>
            <option value="asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'asc') ? 'selected' : '' ?>>
                Oldest to Latest
            </option>
        </select>
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary">Apply</button>
        <a href="ebooks.php" class="btn btn-secondary">Reset</a>
    </div>
</form>

            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>PDF</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $ebooks->fetch_assoc()): ?>
                    <tr>
                        <td><?= nl2br($row['title']) ?></td>
                        <td><?= nl2br($row['author']) ?></td>
                        <td><?= $row['category'] ?></td>
                        <td><?= $row['location'] ?></td>
                        <td><a href="../uploads/ebooks/<?= $row['pdf'] ?>" target="_blank" class="btn btn-sm btn-outline-info">View</a></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this ebook?')" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================= -->
<!--   Add Modal                   -->
<!-- ============================= -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="post" enctype="multipart/form-data" class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Add Ebook</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-2">
            <div class="col-md-6"><label>Title</label><textarea class="form-control" name="title" required></textarea></div>
            <div class="col-md-6"><label>Author</label><textarea class="form-control" name="author" required></textarea></div>
            <div class="col-md-12"><label>Description</label><textarea class="form-control" name="description"></textarea></div>
            <div class="col-md-4"><label>Edition</label><input class="form-control" name="edition"></div>
            <div class="col-md-4"><label>Publisher</label><input class="form-control" name="publisher"></div>
            <div class="col-md-4"><label>Copyright Year</label><input type="number" class="form-control" name="copyrightyear" value="2000"></div>
            <div class="col-md-4"><label>Class</label><input class="form-control" name="class"></div>
            <div class="col-md-4"><label>Subject</label><input class="form-control" name="subject"></div>
            <div class="col-md-4"><label>DOI</label><textarea class="form-control" name="doi"></textarea></div>
            <div class="col-md-6">
                <label>Category</label>
                <select name="category" class="form-select">
                    <option value="" selected disabled>---SELECT CATEGORIES---</option>
                    <?php $categories->data_seek(0); while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['category'] ?>"><?= $cat['category'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label>Location</label>
                <select name="location" class="form-select">
                    <option value="" selected disabled>---SELECT SECTION---</option>
                    <?php $locations->data_seek(0); while($loc = $locations->fetch_assoc()): ?>
                        <option value="<?= $loc['location'] ?>"><?= $loc['location'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6"><label>Cover Image</label><input type="file" name="coverage" class="form-control"></div>
            <div class="col-md-6"><label>PDF File</label><input type="file" name="pdf" class="form-control" required></div>
        </div>
        <div class="modal-footer">
            <button type="submit" name="add_ebook" class="btn btn-primary">Add</button>
        </div>
    </form>
  </div>
</div>

<!-- ============================= -->
<!--   Edit Modals (all rows)      -->
<!-- ============================= -->
<?php $ebooks->data_seek(0); while($row = $ebooks->fetch_assoc()): ?>
<div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg">
    <form method="post" enctype="multipart/form-data" class="modal-content">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <input type="hidden" name="old_coverage" value="<?= $row['coverage'] ?>">
        <input type="hidden" name="old_pdf" value="<?= $row['pdf'] ?>">
        <div class="modal-header bg-warning">
            <h5 class="modal-title">Edit Ebook</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-2">
            <div class="col-md-6"><label>Title</label><textarea class="form-control" name="title" required><?= $row['title'] ?></textarea></div>
            <div class="col-md-6"><label>Author</label><textarea class="form-control" name="author" required><?= $row['author'] ?></textarea></div>
            <div class="col-md-12"><label>Description</label><textarea class="form-control" name="description"><?= $row['description'] ?></textarea></div>
            <div class="col-md-4"><label>Edition</label><input class="form-control" name="edition" value="<?= $row['edition'] ?>"></div>
            <div class="col-md-4"><label>Publisher</label><input class="form-control" name="publisher" value="<?= $row['publisher'] ?>"></div>
            <div class="col-md-4"><label>Copyright Year</label><input type="number" class="form-control" name="copyrightyear" value="<?= $row['copyrightyear'] ?>"></div>
            <div class="col-md-4"><label>Class</label><input class="form-control" name="class" value="<?= $row['class'] ?>"></div>
            <div class="col-md-4"><label>Subject</label><input class="form-control" name="subject" value="<?= $row['subject'] ?>"></div>
            <div class="col-md-4"><label>DOI</label><textarea class="form-control" name="doi"><?= $row['doi'] ?></textarea></div>
            <div class="col-md-6">
                <label>Category</label>
                <select name="category" class="form-select">
                    <option value="" disabled <?= empty($row['category']) ? 'selected' : '' ?>>---SELECT CATEGORIES---</option>
                    <?php $categories->data_seek(0); while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['category'] ?>" <?= ($row['category'] == $cat['category']) ? 'selected' : '' ?>>
                            <?= $cat['category'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label>Location</label>
                <select name="location" class="form-select">
                    <option value="" disabled <?= empty($row['location']) ? 'selected' : '' ?>>---SELECT SECTION---</option>
                    <?php $locations->data_seek(0); while($loc = $locations->fetch_assoc()): ?>
                        <option value="<?= $loc['location'] ?>" <?= ($row['location'] == $loc['location']) ? 'selected' : '' ?>>
                            <?= $loc['location'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label>Current Cover</label>
                <?php if (!empty($row['coverage'])): ?>
                    <p>
                        <a href="../uploads/coverage/<?= $row['coverage'] ?>" target="_blank">
                            <?= htmlspecialchars($row['coverage']) ?>
                        </a>
                    </p>
                <?php else: ?>
                    <p class="text-muted">No cover uploaded</p>
                <?php endif; ?>
                <label>Change Cover</label>
                <input type="file" name="coverage" class="form-control">
            </div>
            <div class="col-md-6">
                <label>Current PDF</label>
                <?php if (!empty($row['pdf'])): ?>
                    <p>
                        <a href="../uploads/ebooks/<?= $row['pdf'] ?>" target="_blank">
                            <?= htmlspecialchars($row['pdf']) ?>
                        </a>
                    </p>
                <?php else: ?>
                    <p class="text-muted">No PDF uploaded</p>
                <?php endif; ?>
                <label>Change PDF</label>
                <input type="file" name="pdf" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" name="update_ebook" class="btn btn-success">Update</button>
        </div>
    </form>
  </div>
</div>
<?php endwhile; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

