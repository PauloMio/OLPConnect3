<div id="sidebar" class="sidebar">

    <!-- Toggle Button -->
    <button id="toggleSidebar" class="toggle-btn" aria-label="Toggle Sidebar">☰</button>

    <ul class="menu">
        <li>
            <a href="/index.php">
                <img src="/images/icons/home.png" class="icon" alt="Home">
                <span class="label">Home</span>
            </a>
        </li>
        <li>
            <a href="/tabs/user/student/logIn.php">
                <img src="/images/icons/login.png" class="icon" alt="Log-In">
                <span class="label">Log-In</span>
            </a>
        </li>
        <li>
            <a href="/tabs/user/student/sign_up.php">
                <img src="/images/icons/signup.png" class="icon" alt="Sign Up">
                <span class="label">Sign Up</span>
            </a>
        </li>
    </ul>
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
    overflow: hidden;
}

.sidebar.collapsed {
    width: 70px;
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
    border-radius: 0;
    margin: 0;
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

#main-content {
    transition: margin-left 0.3s ease;
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

    updateMainContentMargin();

    const resizeObserver = new ResizeObserver(() => updateMainContentMargin());
    resizeObserver.observe(sidebar);
});
</script>
