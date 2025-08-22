<?php
include '../../../database/db_connect.php'; // adjust path

if (!isset($_GET['id'])) {
    die("Ebook ID not specified.");
}

$id = intval($_GET['id']);

// Fetch ebook data
$stmt = $conn->prepare("SELECT * FROM ebooks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Ebook not found.");
}

$ebook = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($ebook['title']) ?> - Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .ebook-cover {
            object-fit: cover;
            height: 300px;
            width: 100%;
        }
        .pdf-container {
            height: 600px;
            width: 100%;
            border: 1px solid #ccc;
            overflow: hidden;
        }
        .pdf-iframe {
            width: 100%;
            height: 100%;
            transform-origin: 0 0;
        }
    </style>
</head>
<body class="bg-light p-4">
<div class="container">
    <h2 class="mb-4 text-center">📖 <?= htmlspecialchars($ebook['title']) ?></h2>

    <!-- Metadata Card -->
    <div class="card mb-4 shadow-sm">
        <?php if (!empty($ebook['coverage']) && file_exists("../../uploads/coverage/".$ebook['coverage'])): ?>
            <img src="../../uploads/coverage/<?= $ebook['coverage'] ?>" class="card-img-top ebook-cover" alt="Cover">
        <?php else: ?>
            <img src="https://via.placeholder.com/600x300?text=No+Cover" class="card-img-top ebook-cover" alt="No Cover">
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

    <!-- PDF Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">📄 PDF Viewer</h5>
            <div class="pdf-container mb-2">
                <?php if (!empty($ebook['pdf']) && file_exists("../../uploads/ebooks/".$ebook['pdf'])): ?>
                    <iframe src="../../uploads/ebooks/<?= $ebook['pdf'] ?>#toolbar=0" 
                            class="pdf-iframe" id="pdfFrame"></iframe>
                <?php else: ?>
                    <p class="text-muted">PDF not available.</p>
                <?php endif; ?>
            </div>

            <!-- Zoom Controls -->
            <div class="d-flex gap-2">
                <button class="btn btn-secondary" onclick="zoomPDF(0.8)">Zoom Out</button>
                <button class="btn btn-secondary" onclick="zoomPDF(1.2)">Zoom In</button>
                <button class="btn btn-secondary" onclick="zoomPDF(1)">Reset Zoom</button>
            </div>
        </div>
    </div>

</div>

<script>
let currentScale = 1;
function zoomPDF(factor) {
    if(factor === 1) { currentScale = 1; }
    else { currentScale *= factor; }
    document.getElementById('pdfFrame').style.transform = `scale(${currentScale})`;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
