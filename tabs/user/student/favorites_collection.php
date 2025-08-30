<?php
session_start();
include '../../../database/db_connect.php';

// ✅ Correct session check
if (!isset($_SESSION['account'])) {
    header("Location: ../../../index.php");
    exit;
}

// Get logged-in user ID
$account_id = $_SESSION['account']['id'];

// ✅ Working query to fetch favorites
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
    font-family: Arial, sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
}
#main-content { padding: 20px; margin-left: 240px; }
.ebook-card { border-radius: 12px; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; }
.ebook-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
.cover-wrapper { width: 100%; height: 250px; display: flex; justify-content: center; align-items: center; background: #f8f9fa; overflow: hidden; }
.cover-wrapper img { width: 100%; height: 100%; object-fit: cover; }
.card-body { display: flex; flex-direction: column; }
.favorite-btn { border: none; background: transparent; font-size: 1.5rem; cursor: pointer; }
.text-truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="container p-4" id="main-content">
    <h2 class="mb-4 text-center">⭐ My Favorite Ebooks</h2>
    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card ebook-card h-100">
                        <div class="cover-wrapper">
                            <?php 
                            // ✅ Correct cover image loading
                            $server_path = __DIR__ . "/../../uploads/coverage/" . $row['coverage'];
                            $img_url = "../../uploads/coverage/" . $row['coverage'];
                            ?>
                            <?php if (!empty($row['coverage']) && file_exists($server_path)): ?>
                                <img src="<?= $img_url ?>" alt="Cover Image">
                            <?php else: ?>
                                <img src="../../../images/icons/defaultcover.png" alt="No Cover">
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate"><?= htmlspecialchars($row['title']); ?></h5>
                            <p class="card-text mb-1"><strong>Author:</strong> <?= htmlspecialchars($row['author']); ?></p>
                            <p class="card-text mb-1"><strong>Category:</strong> <?= htmlspecialchars($row['category']); ?></p>
                            <p class="card-text mb-1"><strong>Year:</strong> <?= htmlspecialchars($row['copyrightyear']); ?></p>
                            <p class="card-text mb-3 text-truncate"><strong>Location:</strong> <?= htmlspecialchars($row['location']); ?></p>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <a href="ebook_details.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">📖 Read More</a>
                                <form action="toggle_favorite.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="ebook_id" value="<?= $row['id']; ?>">
                                    <button type="submit" class="favorite-btn" title="Remove from favorites">⭐</button>
                                </form>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
