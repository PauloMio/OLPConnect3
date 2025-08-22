<?php
include '../../../database/db_connect.php'; // Correct path based on project structure

// Get ebook ID from query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Ebook ID");
}

$id = intval($_GET['id']);

// Fetch ebook from database
$stmt = $conn->prepare("SELECT * FROM ebooks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$ebook = $stmt->get_result()->fetch_assoc();

if (!$ebook) {
    die("Ebook not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($ebook['title']) ?> - Details</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f0f2f5; padding: 20px; font-family: Arial, sans-serif; }
.card { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 30px; }
.card1 { display: flex; gap: 20px; flex-wrap: wrap; }
.cover-photo { width: 300px; height: 400px; object-fit: cover; border-radius: 8px; }
.details { flex: 1; }
.details h2 { margin-top: 0; }
.card2 { background: #121212; color: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.3); }
.controls { position: sticky; top: 0; text-align: center; background: #121212; padding: 10px 0; z-index: 10; }
.controls button { padding: 8px 12px; margin: 0 5px; border: none; background-color: #007BFF; color: white; border-radius: 5px; cursor: pointer; }
.controls button:hover { background-color: #0056b3; }
.zoom-info { display: inline-block; margin: 0 10px; font-weight: bold; }
#pdf-scroll-container { max-height: 800px; overflow-y: auto; padding: 10px 0; }
#pdf-container { display: flex; flex-direction: column; align-items: center; }
canvas { margin-bottom: 20px; border-radius: 8px; box-shadow: 0 1px 5px rgba(0,0,0,0.1); }
</style>
</head>
<body oncontextmenu="return false;">

<!-- Card 1: Ebook Details -->
<div class="card card1">
    <?php if (!empty($ebook['coverage']) && file_exists("../../uploads/coverage/".$ebook['coverage'])): ?>
        <img src="../../uploads/coverage/<?= htmlspecialchars($ebook['coverage']) ?>" alt="Cover Photo" class="cover-photo">
    <?php else: ?>
        <img src="https://via.placeholder.com/300x400?text=No+Cover" class="cover-photo" alt="Default Cover">
    <?php endif; ?>

    <div class="details">
        <h2><?= htmlspecialchars($ebook['title']) ?></h2>
        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($ebook['description'])) ?></p>
        <p><strong>Edition:</strong> <?= htmlspecialchars($ebook['edition']) ?></p>
        <p><strong>Author:</strong> <?= htmlspecialchars($ebook['author']) ?></p>
        <p><strong>Category:</strong> <?= htmlspecialchars($ebook['category']) ?></p>
        <p><strong>Publisher:</strong> <?= htmlspecialchars($ebook['publisher']) ?></p>
        <p><strong>Copyright Year:</strong> <?= htmlspecialchars($ebook['copyrightyear']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($ebook['location']) ?></p>
    </div>
</div>

<!-- Card 2: PDF Viewer -->
<div class="card2">
    <?php if (!empty($ebook['pdf']) && file_exists("../../uploads/ebooks".$ebook['pdf'])): ?>
    <div class="controls">
        <button onclick="zoomOut()">-</button>
        <span id="zoom-info">125%</span>
        <button onclick="zoomIn()">+</button>
    </div>
    <div id="pdf-scroll-container">
        <div id="pdf-container"></div>
    </div>
</div>

<!-- PDF.js -->
<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>
<script>
const url = "<?= '/OLPConnect3/tabs/uploads/ebooks/' . htmlspecialchars($ebook['pdf']) ?>"; // <-- web path
const container = document.getElementById('pdf-container');
let scale = 1.00;
let pdfDoc = null;

function renderAllPages(pdf) {
    container.innerHTML = '';
    for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
        pdf.getPage(pageNum).then(page => {
            const viewport = page.getViewport({ scale });
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            page.render({ canvasContext: ctx, viewport: viewport });
            container.appendChild(canvas);
        });
    }
}

function zoomIn() {
    scale = Math.min(scale + 0.25, 3);
    document.getElementById('zoom-info').textContent = Math.round(scale*100)+"%";
    renderAllPages(pdfDoc);
}

function zoomOut() {
    scale = Math.max(scale - 0.25, 0.5);
    document.getElementById('zoom-info').textContent = Math.round(scale*100)+"%";
    renderAllPages(pdfDoc);
}

pdfjsLib.getDocument(url).promise.then(pdf => {
    pdfDoc = pdf;
    renderAllPages(pdfDoc);
}).catch(err => {
    container.innerHTML = "<p style='color:red'>Failed to load PDF.</p>";
    console.error(err);
});
</script>
<?php endif; ?>
</body>
</html>
