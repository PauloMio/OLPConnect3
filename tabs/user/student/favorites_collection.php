<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['account_id'])) {
    header("Location: logIn.php");
    exit;
}

include '../../../database/db_connect.php';
$account_id = $_SESSION['account_id'];

// Fetch favorites
$query = "SELECT e.* 
          FROM account_ebook_favorite f
          JOIN ebooks e ON f.ebook_id = e.id
          WHERE f.account_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        #main-content {
            transition: margin-left 0.3s ease;
            flex: 1;
        }
        .sidebar-open #main-content { margin-left: 220px; }
        .sidebar-closed #main-content { margin-left: 70px; }

        .ebook-card {
            transition: transform 0.2s;
        }
        .ebook-card:hover {
            transform: translateY(-5px);
        }

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

        .favorite-btn {
            border: none;
            background: none;
            font-size: 22px;
            cursor: pointer;
            color: gold;
        }
    </style>
</head>
<body class="sidebar-open">

<?php include "sidebar.php"; ?>

<div class="content" id="main-content">
    <div class="container p-4">
        <h2 class="mb-4 text-center">⭐ My Favorite Ebooks</h2>

        <div class="row g-4">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
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

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="ebook_details.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">You have no favorite ebooks yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".favorite-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            const ebookId = this.dataset.ebookId;
            fetch("toggle_favorite.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "ebook_id=" + ebookId
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (!data.is_favorite) {
                        this.closest(".col-12.col-sm-6.col-md-4.col-lg-3").remove(); // Remove card if unfavorited
                    }
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => console.error("Favorite toggle failed:", err));
        });
    });
});
</script>
</body>
</html>
