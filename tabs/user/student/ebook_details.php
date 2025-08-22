<?php
include '../../../database/db_connect.php'; // adjust path

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No ebook selected.");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM ebooks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$ebook = $stmt->get_result()->fetch_assoc();

if (!$ebook) {
    die("Ebook not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($ebook['title']) ?> - Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .ebook-card img {
            object-fit: cover;
            height: 250px;
            width: 100%;
        }
        .pdf-viewer {
            height: 600px;
            width: 100%;
            border: none;
        }
        .zoom-control {
            max-width: 120px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="bg-light p-4">
<div class="container">
    <h2 class="mb-4 text-center">📚 Ebook Details</h2>

    <div class="row g-4 mb-4">
        <!-- Card 1: Ebook Info -->
        <div class="col-12">
            <div class="card shadow-sm ebook-card">
                <?php if (!empty($ebook['coverage']) && file_exists("../../uploads/coverage/".$ebook['coverage'])): ?>
                    <img src="../../uploads/coverage/<?= $ebook['coverage'] ?>" class="card-img-top" alt="Cover Image">
                <?php else: ?>
                    <img src="https://via.placeholder.com/600x250?text=No+Cover" class="card-img-top" alt="No Cover">
                <?php endif; ?>
                <div class="card-body">
                    <h3 class="card-title"><?= htmlspecialchars($ebook['title']) ?></h3>
                    <p class="card-text"><strong>Author:</strong> <?= htmlspecialchars($ebook['author']) ?></p>
                    <p class="card-text"><strong>Description:</strong> <?= nl2br(htmlspecialchars($ebook['description'])) ?></p>
                    <p class="card-text"><strong>Category:</strong> <?= htmlspecialchars($ebook['category']) ?></p>
                    <p class="card-text"><strong>Year:</strong> <?= htmlspecialchars($ebook['copyrightyear']) ?></p>
                    <p class="card-text"><strong>Location:</strong> <?= htmlspecialchars($ebook['location']) ?></p>
                </div>
            </div>
        </div>

        <!-- Card 2: Scrollable PDF -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Read PDF</h5>

                    <!-- Zoom control -->
                    <label for="zoomLevel" class="form-label">Zoom (%)</label>
                    <input type="number" id="zoomLevel" class="form-control zoom-control" value="100" min="25" max="400">

                    <?php if (!empty($ebook['pdf']) && file_exists("../../uploads/ebooks/".$ebook['pdf'])): ?>
                        <iframe id="pdfViewer" class="pdf-viewer" 
                                src="../../uploads/ebooks/<?= $ebook['pdf'] ?>#toolbar=0&navpanes=0&scrollbar=1&zoom=100" 
                                type="application/pdf">
                        </iframe>
                    <?php else: ?>
                        <p class="text-muted">PDF not available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <a href="ebook_collection.php" class="btn btn-secondary">Back to Collection</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const zoomInput = document.getElementById('zoomLevel');
    const pdfViewer = document.getElementById('pdfViewer');

    zoomInput.addEventListener('input', function() {
        let zoom = parseInt(this.value);
        if (zoom < 25) zoom = 25;
        if (zoom > 400) zoom = 400;
        this.value = zoom;
        const baseSrc = "/OLPConnect3/uploads/ebooks/<?= $ebook['pdf'] ?>"
        pdfViewer.src = `${baseSrc}#toolbar=0&navpanes=0&scrollbar=1&zoom=${zoom}`;
    });
</script>
</body>
</html>
