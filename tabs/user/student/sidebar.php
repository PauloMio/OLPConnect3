<?php
include '../../../database/db_connect.php';

// Handle logout
if (isset($_POST['logout']) && isset($_SESSION['account'])) {
    $id = $_SESSION['account']['id'];
    $stmt = $conn->prepare("UPDATE account SET status='inactive', loggedout=NOW() WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    session_destroy();
    header("Location: ../../../index.php");
    exit;
}

$account = $_SESSION['account'] ?? null;
?>

<div id="sidebar" class="sidebar">

    <!-- Toggle Button (now below user info) -->
    <button id="toggleSidebar" class="toggle-btn" aria-label="Toggle Sidebar">☰</button>

    <?php if ($account): ?>
    <div class="account-info p-3 border-bottom">
        <strong><?= htmlspecialchars($account['firstname'] . " " . $account['lastname']) ?></strong><br>
        <small>ID: <?= htmlspecialchars($account['schoolid']) ?></small>
    </div>
    <?php endif; ?>

    <ul class="menu">
        <li>
            <a href="ebook_collection.php">
                <img src="../../../images/icons/Books.png" class="icon" alt="eBooks">
                <span class="label">Ebooks</span>
            </a>
        </li>
        <li>
            <a href="#">
                <img src="../../../images/icons/favorite.png" class="icon" alt="Favorites">
                <span class="label">Favorites</span>
            </a>
        </li>
    </ul>

    <?php if ($account): ?>
    <div class="logout-section">
        <form method="POST">
            <button type="submit" name="logout" class="logout-btn">Log out</button>
        </form>
    </div>
    <?php endif; ?>
</div>

<!-- keep your existing CSS + JS for sidebar -->


<style>
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 240px;
    background-color: #2c3e50;
    color: white;
    display: flex;
    flex-direction: column;
    transition: width 0.3s ease;
    z-index: 1000;
    overflow: hidden;
}

.sidebar.collapsed {
    width: 70px;
}

/* Hide account info when sidebar is collapsed */
.sidebar.collapsed .account-info {
    display: none;
}

/* Toggle button pinned to the left */
.toggle-btn {
    background: #1a252f;
    border: none;
    color: white;
    font-size: 22px;
    width: 100%;
    height: 40px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0;
    margin: 0;
}
.toggle-btn:hover {
    background: #34495e;
}

.menu {
    list-style: none;
    padding: 60px 0 0 0; /* push content below toggle */
    margin: 0;
    flex-grow: 1;
}

.menu li {
    margin: 0;
}

.menu a {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    text-decoration: none;
    color: white;
    transition: background 0.2s ease;
    white-space: nowrap;
}

.menu a:hover {
    background-color: #34495e;
}

.icon {
    width: 24px;
    height: 24px;
    filter: brightness(0) invert(1);
    flex-shrink: 0;
}

.label {
    margin-left: 1rem;
    transition: opacity 0.2s ease, margin 0.2s ease;
    opacity: 1;
}

.sidebar.collapsed .label {
    opacity: 0;
    margin-left: -9999px;
}

.logout-section {
    padding: 1rem;
    border-top: 1px solid #34495e;
}

.logout-btn {
    width: 100%;
    background-color: #dc3545;
    border: none;
    padding: 8px 0;
    border-radius: 4px;
    color: white;
    cursor: pointer;
    transition: background-color 0.2s;
}

.logout-btn:hover {
    background-color: #b02a37;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const mainContent = document.getElementById('main-content');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        updateMainContentMargin();
    });

    function updateMainContentMargin() {
        const sidebarWidth = sidebar.offsetWidth;
        if (mainContent) {
            mainContent.style.marginLeft = sidebarWidth + 'px';
        }
    }

    // Initial content shift
    updateMainContentMargin();

    // Auto-update margin on resize
    const resizeObserver = new ResizeObserver(() => updateMainContentMargin());
    resizeObserver.observe(sidebar);
});
</script>
