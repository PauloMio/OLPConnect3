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

// Fetch categories, programs, and departments for dropdowns
$categories = $conn->query("SELECT id, category FROM research_category ORDER BY category ASC");
$programs   = $conn->query("SELECT id, program FROM program_user ORDER BY program ASC");
$departments= $conn->query("SELECT id, department FROM tbl_department ORDER BY department ASC");

// Handle Create
if (isset($_POST['create'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $year = $_POST['year'];
    $category = $_POST['category'];
    $program = $_POST['program'];
    $department = $_POST['department'];
    $accession_no = $_POST['accession_no'];

    $stmt = $conn->prepare("INSERT INTO research (title, author, year, category, program, Department, accession_no, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssssss", $title, $author, $year, $category, $program, $department, $accession_no);
    $stmt->execute();
    $stmt->close();
    header("Location: research.php");
    exit;
}

// Handle Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $year = $_POST['year'];
    $category = $_POST['category'];
    $program = $_POST['program'];
    $department = $_POST['department'];
    $accession_no = $_POST['accession_no'];

    $stmt = $conn->prepare("UPDATE research SET title=?, author=?, year=?, category=?, program=?, Department=?, accession_no=?, updated_at=NOW() WHERE id=?");
    $stmt->bind_param("sssssssi", $title, $author, $year, $category, $program, $department, $accession_no, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: research.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM research WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: research.php");
    exit;
}

// Filtering & Searching
$filterCategory = isset($_GET['filter_category']) ? $_GET['filter_category'] : '';
$searchQuery    = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM research WHERE 1";
$params = [];
$types = "";

// Apply category filter
if ($filterCategory !== '') {
    $sql .= " AND category=?";
    $params[] = $filterCategory;
    $types .= "s";
}

// Apply search filter
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Research CRUD</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div id="main-content" style="padding:20px;">
    <div class="container mt-5">
    <h2 class="mb-4">Research CRUD</h2>
    
    <div class="row mb-3">
        <div class="col-md-3">
            <!-- Category Filter -->
            <form method="GET" id="filterForm">
                <select name="filter_category" class="form-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All Categories</option>
                    <?php
                    $categories = $conn->query("SELECT id, category FROM research_category ORDER BY category ASC");
                    while($c = $categories->fetch_assoc()):
                        $selected = ($c['category'] == $filterCategory) ? "selected" : "";
                    ?>
                    <option value="<?= htmlspecialchars($c['category']) ?>" <?= $selected ?>><?= htmlspecialchars($c['category']) ?></option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>
        <div class="col-md-6">
            <!-- Search Box -->
            <form method="GET">
                <input type="hidden" name="filter_category" value="<?= htmlspecialchars($filterCategory) ?>">
                <input type="text" name="search" class="form-control" placeholder="Search by Title or Author" 
       value="<?= htmlspecialchars($searchQuery) ?>" 
       onkeypress="if(event.key==='Enter'){this.form.submit();}" 
       id="searchBox">
            </form>
        </div>
        <div class="col-md-3 text-end">
            <!-- Add Research button -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                Add Research
            </button>
        </div>
    </div>

    <table class="table table-bordered">
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
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
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
                <td><?= $row['created_at'] ?></td>
                <td><?= $row['updated_at'] ?></td>
                <td>
                    <button class="btn btn-sm btn-warning editBtn"
                        data-id="<?= $row['id'] ?>"
                        data-title="<?= htmlspecialchars($row['title']) ?>"
                        data-author="<?= htmlspecialchars($row['author']) ?>"
                        data-year="<?= htmlspecialchars($row['year']) ?>"
                        data-category="<?= htmlspecialchars($row['category']) ?>"
                        data-program="<?= htmlspecialchars($row['program']) ?>"
                        data-department="<?= htmlspecialchars($row['Department']) ?>"
                        data-accession="<?= htmlspecialchars($row['accession_no']) ?>"
                        data-bs-toggle="modal" data-bs-target="#editModal">
                        Edit
                    </button>
                    <a href="research.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Research</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <textarea class="form-control" name="title" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Author</label>
                    <textarea class="form-control" name="author" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Year</label>
                    <input type="text" class="form-control" name="year">
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">Select Category</option>
                        <?php
                        $categories = $conn->query("SELECT id, category FROM research_category ORDER BY category ASC");
                        while($c = $categories->fetch_assoc()):
                        ?>
                        <option value="<?= htmlspecialchars($c['category']) ?>"><?= htmlspecialchars($c['category']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Program</label>
                    <select name="program" class="form-control">
                        <option value="">Select Program</option>
                        <?php
                        $programs = $conn->query("SELECT id, program FROM program_user ORDER BY program ASC");
                        while($p = $programs->fetch_assoc()):
                        ?>
                        <option value="<?= htmlspecialchars($p['program']) ?>"><?= htmlspecialchars($p['program']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <select name="department" class="form-control">
                        <option value="">Select Department</option>
                        <?php
                        $departments = $conn->query("SELECT id, department FROM tbl_department ORDER BY department ASC");
                        while($d = $departments->fetch_assoc()):
                        ?>
                        <option value="<?= htmlspecialchars($d['department']) ?>"><?= htmlspecialchars($d['department']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Accession No</label>
                    <input type="text" class="form-control" name="accession_no">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="create" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Research</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <textarea class="form-control" id="edit_title" name="title" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Author</label>
                    <textarea class="form-control" id="edit_author" name="author" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Year</label>
                    <input type="text" class="form-control" id="edit_year" name="year">
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select id="edit_category" name="category" class="form-control">
                        <option value="">Select Category</option>
                        <?php
                        $categories = $conn->query("SELECT id, category FROM research_category ORDER BY category ASC");
                        while($c = $categories->fetch_assoc()):
                        ?>
                        <option value="<?= htmlspecialchars($c['category']) ?>"><?= htmlspecialchars($c['category']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Program</label>
                    <select id="edit_program" name="program" class="form-control">
                        <option value="">Select Program</option>
                        <?php
                        $programs = $conn->query("SELECT id, program FROM program_user ORDER BY program ASC");
                        while($p = $programs->fetch_assoc()):
                        ?>
                        <option value="<?= htmlspecialchars($p['program']) ?>"><?= htmlspecialchars($p['program']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <select id="edit_department" name="department" class="form-control">
                        <option value="">Select Department</option>
                        <?php
                        $departments = $conn->query("SELECT id, department FROM tbl_department ORDER BY department ASC");
                        while($d = $departments->fetch_assoc()):
                        ?>
                        <option value="<?= htmlspecialchars($d['department']) ?>"><?= htmlspecialchars($d['department']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Accession No</label>
                    <input type="text" class="form-control" id="edit_accession_no" name="accession_no">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="update" class="btn btn-success">Update</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
  </div>
</div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const editBtns = document.querySelectorAll('.editBtn');
editBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_title').value = btn.dataset.title;
        document.getElementById('edit_author').value = btn.dataset.author;
        document.getElementById('edit_year').value = btn.dataset.year;
        document.getElementById('edit_category').value = btn.dataset.category;
        document.getElementById('edit_program').value = btn.dataset.program;
        document.getElementById('edit_department').value = btn.dataset.department;
        document.getElementById('edit_accession_no').value = btn.dataset.accession;
    });
});

// textbox search empty default:
const searchBox = document.getElementById('searchBox');
searchBox.addEventListener('input', function() {
    if(this.value.trim() === '') {
        // submit the form without search to reset the table
        this.form.submit();
    }
});
</script>
</body>
</html>
