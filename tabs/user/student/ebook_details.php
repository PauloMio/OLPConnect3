<?php
include '../../../database/db_connect.php';

if (!isset($_GET['id'])) {
    die("No ebook selected.");
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM ebooks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Ebook not found.");
}
$ebook = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($ebook['title']) ?> - Ebook Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .pdf-container {
        background-color: black;
        padding: 20px;
        text-align: center;
        max-height: 80vh;         /* limit height for scrolling */
        overflow-y: auto;         /* scroll vertically */
        position: relative;
        }
        .pdf-page {
            display: inline-block;
            margin-bottom: 20px;
        }
        canvas {
            display: block;
            margin: 0 auto;
            border-radius: 5px;
        }

        /* Sticky zoom controls */
        .zoom-controls {
            position: sticky;
            top: 0;                  /* stick to the top */
            display: flex;
            justify-content: center;
            background: rgba(0,0,0,0.8);  /* semi-transparent background */
            padding: 10px;
            z-index: 10;
            border-radius: 5px;
        }

        #zoomIn, #zoomOut {
            font-size: 1rem; /* bigger text for + and - */
            padding: 0.5rem 1rem; /* adjust button size */
        }
    </style>
</head>
<body class="bg-light p-4">

<div class="container">
    <a href="ebook_collection.php" class="btn btn-secondary mb-4">← Back to Collection</a>
    
        <!-- Card 1: Metadata -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <!-- Cover Image -->
                <div class="col-md-3 d-flex align-items-start">
                    <?php if (!empty($ebook['coverage']) && file_exists("../../uploads/coverage/".$ebook['coverage'])): ?>
                        <img src="../../uploads/coverage/<?= $ebook['coverage'] ?>" 
                             class="img-fluid rounded shadow-sm" 
                             style="max-width: 100%; height: auto;" 
                             alt="Cover Image">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/200x300?text=No+Cover" 
                             class="img-fluid rounded shadow-sm" 
                             alt="No Cover">
                    <?php endif; ?>
                </div>

                <!-- Metadata -->
                <div class="col-md-9">
                    <h4 class="card-title"><?= htmlspecialchars($ebook['title']) ?></h4>
                    <p><strong>Author:</strong> <?= htmlspecialchars($ebook['author']) ?></p>
                    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($ebook['description'])) ?></p>
                    <p><strong>Category:</strong> <?= htmlspecialchars($ebook['category']) ?></p>
                    <p><strong>Year:</strong> <?= htmlspecialchars($ebook['copyrightyear']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($ebook['location']) ?></p>
                </div>
            </div>
        </div>
    </div>


    <!-- Card 2: PDF Viewer -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-center">📖 Ebook Viewer</h5>

            <!-- Zoom Controls (sticky center) -->
            <div class="zoom-controls">
                <button id="zoomIn" class="btn btn-sm btn-primary mx-1">+</button>
                <button id="zoomOut" class="btn btn-sm btn-danger mx-1">-</button>
            </div>

            <!-- PDF Container -->
            <div class="pdf-container" id="pdf-container"></div>
        </div>
    </div>
</div>

<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
const url = "../../uploads/ebooks/<?= $ebook['pdf'] ?>"; // adjust path if needed

let pdfDoc = null,
    scale = 1.2,
    pdfContainer = document.getElementById('pdf-container');

const renderAllPages = () => {
    pdfContainer.innerHTML = ''; // clear previous render
    for (let num = 1; num <= pdfDoc.numPages; num++) {
        pdfDoc.getPage(num).then(page => {
            let viewport = page.getViewport({ scale: scale });
            let canvas = document.createElement('canvas');
            let ctx = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            let wrapper = document.createElement('div');
            wrapper.classList.add("pdf-page");
            wrapper.appendChild(canvas);
            pdfContainer.appendChild(wrapper);

            page.render({ canvasContext: ctx, viewport: viewport });
        });
    }
};

pdfjsLib.getDocument(url).promise.then(pdfDoc_ => {
    pdfDoc = pdfDoc_;
    renderAllPages();
});

document.getElementById('zoomIn').addEventListener('click', () => {
    scale += 0.2;
    renderAllPages();
});

document.getElementById('zoomOut').addEventListener('click', () => {
    if (scale > 0.4) {
        scale -= 0.2;
        renderAllPages();
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
