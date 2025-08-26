<!-- sidebar.php -->
<style>
    body {
        margin: 0;
        padding: 0;
        display: flex;
    }

    /* Sidebar base */
    #sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        background: #343a40;
        color: #fff;
        transition: width 0.3s;
        overflow-x: hidden;
        padding-top: 20px;
        z-index: 1000;
    }

    /* Collapsed state */
    #sidebar.collapsed {
        width: 70px;
    }

    /* Sidebar links */
    #sidebar a {
        display: flex;
        align-items: center;
        color: #fff;
        padding: 10px 15px;
        text-decoration: none;
        transition: background 0.2s;
    }
    #sidebar a:hover {
        background: #495057;
    }
    #sidebar img {
        width: 24px;
        height: 24px;
        margin-right: 10px;
    }

    /* Hide text when collapsed */
    #sidebar.collapsed a span {
        display: none;
    }

    /* Toggle button */
    #sidebar-toggle {
        position: absolute;
        top: 15px;
        right: -15px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        z-index: 1100;
    }

    /* Content area shifts */
    .content {
        margin-left: 250px;
        padding: 20px;
        transition: margin-left 0.3s;
        width: 100%;
    }
    .content.expanded {
        margin-left: 70px;
    }
</style>

<div id="sidebar">
    <button id="sidebar-toggle">☰</button>
    <a href="ebook_collection.php">
        <img src="../../../images/icons/EbookIcon.png" alt="Ebooks">
        <span>Ebooks</span>
    </a>
    <a href="#">
        <img src="../../../images/icons/FavoriteIcon.png" alt="Favorites">
        <span>Favorites</span>
    </a>
    <a href="#">
        <img src="../../../images/icons/EditIcon.png" alt="Edit">
        <span>Edit Account</span>
    </a>
    <a href="#">
        <img src="../../../images/icons/LogoutIcon.png" alt="Logout">
        <span>Log out</span>
    </a>
</div>

<script>
    document.getElementById("sidebar-toggle").addEventListener("click", function () {
        let sidebar = document.getElementById("sidebar");
        let content = document.getElementById("main-content");
        sidebar.classList.toggle("collapsed");
        content.classList.toggle("expanded");
    });
</script>
