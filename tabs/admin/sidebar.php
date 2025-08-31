<?php
include '../../database/db_connect.php';

// Handle logout
if (isset($_POST['logout']) && isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];
    $stmt = $conn->prepare("UPDATE users SET status='inactive' WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    session_destroy();
    header("Location: login_admin.php");
    exit;
}

// Fetch logged-in user
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
?>

<div id="sidebar" class="sidebar">

    <!-- Toggle Button -->
    <button id="toggleSidebar" class="toggle-btn" aria-label="Toggle Sidebar">☰</button>

    <?php if ($user): ?>
    <div class="account-info p-3 border-bottom">
        <strong><?= htmlspecialchars($user['username']) ?></strong><br>
        <small><?= htmlspecialchars($user['email']) ?></small>
    </div>
    <?php endif; ?>

    <ul class="menu">
        <li>
            <a href="dashboard.php">
                <img src="../../images/icons/dashboard.png" class="icon" alt="Dashboard">
                <span class="label">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="ebooks.php">
                <img src="../../images/icons/Books.png" class="icon" alt="Ebooks">
                <span class="label">Ebooks</span>
            </a>
        </li>
        <li>
            <a href="accounts.php">
                <img src="../../images/icons/user_setup.png" class="icon" alt="Accounts">
                <span class="label">Accounts</span>
            </a>
        </li>
        <li>
            <a href="guest_record.php">
                <img src="../../images/icons/admin.png" class="icon" alt="Guests">
                <span class="label">Guest Records</span>
            </a>
        </li>
        <li>
            <a href="admin.php">
                <img src="../../images/icons/admin_setup.png" class="icon" alt="Guests">
                <span class="label">Admin</span>
            </a>
        </li>
        <li>
            <a href="research.php">
                <img src="../../images/icons/research.png" class="icon" alt="Research">
                <span class="label">Research</span>
            </a>
        </li>

        <!-- DROPDOWN SECTION -->
        <li class="dropdown">
            <a href="#" class="dropdown-toggle">
                <img src="../../images/icons/Setup.png" class="icon" alt="Settings">
                <span class="label">Manage</span>
            </a>
            <ul class="submenu">
                <li><a href="announcement.php">Announcement</a></li>
                <li><a href="department.php">Department</a></li>
                <li><a href="ebook_categories.php">Ebook Categories</a></li>
                <li><a href="ebook_location.php">Ebook Location</a></li>
                <li><a href="program_user.php">Program User</a></li>
                <li><a href="research_category.php">Research Category</a></li>
            </ul>
        </li>
    </ul>
    

    <?php if ($user): ?>
    <div class="logout-section">
        <form method="POST">
            <button type="submit" name="logout" class="logout-btn">Log out</button>
        </form>
    </div>
    <?php endif; ?>
</div>


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
    overflow-y: auto;
}

.sidebar.collapsed {
    width: 70px;
}

.sidebar.collapsed .account-info {
    display: none;
}

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
}
.toggle-btn:hover {
    background: #34495e;
}

.menu {
    list-style: none;
    padding: 60px 0 0 0;
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
    width: 22px;
    height: 22px;
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

/* DROPDOWN */
.dropdown .submenu {
    display: none;
    list-style: none;
    padding-left: 1.5rem;
    background: #34495e;
}

.dropdown.open .submenu {
    display: block;
}

.submenu li a {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
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

    // Collapse/expand sidebar
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');

        // Close all dropdowns if sidebar is collapsed
        if (sidebar.classList.contains('collapsed')) {
            document.querySelectorAll('.dropdown').forEach(drop => {
                drop.classList.remove('open');
            });
        }

        updateMainContentMargin();
    });

    // Dropdown toggle
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            // Prevent opening dropdown when collapsed
            if (!sidebar.classList.contains('collapsed')) {
                toggle.parentElement.classList.toggle('open');
            }
        });
    });

    function updateMainContentMargin() {
        const sidebarWidth = sidebar.offsetWidth;
        if (mainContent) {
            mainContent.style.marginLeft = sidebarWidth + 'px';
        }
    }

    updateMainContentMargin();

    const resizeObserver = new ResizeObserver(() => updateMainContentMargin());
    resizeObserver.observe(sidebar);
});
</script>
