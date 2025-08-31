<?php
// Include database connection
include 'database/db_connect.php';

// Fetch announcements from the database
$sql = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);
$announcements = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OLPCC E-Library</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            min-height: 100vh;
            margin: 0;
        }

        /* Sidebar included styles */
        #main-content {
            flex-grow: 1;
            margin-left: 240px; /* default sidebar width */
            transition: margin-left 0.3s ease;
        }

        .sidebar.collapsed + #main-content {
            margin-left: 70px; /* collapsed sidebar width */
        }

        header {
            padding: 1rem;
            text-align: center;
        }

        #announcementCarousel {
            max-width: 800px;
            height: 400px;
            margin: 2rem auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }

        .section {
            padding: 2rem;
            text-align: center;
        }

        .members, .services {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
        }

        .card {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 200px;
        }

        .card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin: 0 auto;
            display: block;
        }

        .card h3, .card h4 {
            margin: 0.5rem 0 0.2rem;
        }

        .card p {
            margin: 0;
            font-size: 0.9rem;
            color: #555;
        }
    </style>
</head>
<body>

<!-- Include Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main content -->
<div id="main-content">
    <header>
        <h1>OLPCC E-Library</h1>
    </header>

    <!-- Image Carousel -->
    <div class="container">
        <div id="announcementCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach($announcements as $index => $announcement): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <img src="tabs/uploads/announcement/<?= htmlspecialchars($announcement['image_path']) ?>" alt="Announcement Image">
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#announcementCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#announcementCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <!-- Members Section -->
    <section class="section">
        <h2>Meet Our Members</h2>
        <div class="members">
            <div class="card">
                <h4>Myrna L. Macarubbo, RL, MSLS</h4>
                <p>Chief Librarian</p>
            </div>
            <div class="card">
                <h4>Jona V. Castilla, RL, LPT</h4>
                <p>Librarian Elementary Department</p>
            </div>
            <div class="card">
                <h4>Rhea Jane L. Tumaliuan, RL, MLIS</h4>
                <p>Technical Librarian College-Graduate School Department</p>
            </div>
            <div class="card">
                <h4>Jamaica B. Magmanlac</h4>
                <p>Assistant Librarian College Department</p>
            </div>
            <div class="card">
                <h4>Rugene M. Mejia, jr., LPT</h4>
                <p>Support Staff College Department</p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="section" style="background-color: #eef2ff;">
        <h2>Our Services</h2>
        <div class="services">
            <div class="card">
                <h3>Book Circulation</h3>
                <p>Borrowing and returning of books.</p>
            </div>
            <div class="card">
                <h3>OLP Connect</h3>
                <p>Website viewing of eBooks.</p>
            </div>
            <div class="card">
                <h3>Internet Library</h3>
                <p>Public access computers for students to use.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-5 mt-auto">
        <div class="container">
            <p class="mb-4">&copy; <?php echo date("Y"); ?> CITE DEPARTMENT. All rights reserved.</p>
            <div class="d-flex justify-content-center gap-4 flex-wrap">
                <a href="https://www.facebook.com/zyril.evangelista.9" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-none">Zyril Evangelista</a>
                <a href="https://www.facebook.com/ernest.ramones.3" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-none">King Ernest Ramones</a>
                <a href="https://www.facebook.com/paulo.mio.cortez.panopio" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-none">Paulo Mio Panopio</a>
            </div>
        </div>
    </footer>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
