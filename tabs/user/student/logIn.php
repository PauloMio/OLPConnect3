<?php
session_start();
include '../../../database/db_connect.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schoolid  = $_POST['schoolid'];
    $birthdate = $_POST['birthdate'];

    $stmt = $conn->prepare("SELECT * FROM account WHERE schoolid=? AND birthdate=? LIMIT 1");
    $stmt->bind_param("ss", $schoolid, $birthdate);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();
    $stmt->close();

    if ($account) {
        $stmt = $conn->prepare("UPDATE account SET status='active', loggedin=NOW() WHERE id=?");
        $stmt->bind_param("i", $account['id']);
        $stmt->execute();
        $stmt->close();

        $_SESSION['account'] = $account;
        header("Location: ebook_collection.php");
        exit;
    } else {
        $error = "Invalid Student ID or Birthdate.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    display: flex;
    min-height: 100vh;
    margin: 0;
    font-family: Arial, sans-serif;
}

#main-content {
    flex-grow: 1;
    margin-left: 240px; /* default sidebar width */
    display: flex;
    flex-direction: column;
}

.sidebar.collapsed + #main-content {
    margin-left: 70px;
}

.login-wrapper {
    flex: 1; /* take remaining vertical space */
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
}

.login-card {
    max-width: 400px;
    width: 100%;
}

/* Footer spans full width */
footer {
    width: 100%;
}
</style>
</head>
<body>

<!-- Include Sidebar -->
<?php include '../../../sidebar.php'; ?>

<!-- Main Content -->
<div id="main-content">
    <div class="login-wrapper">
        <div class="card p-4 shadow login-card">
            <h3 class="mb-3 text-center">Student Login</h3>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Student ID</label>
                    <input type="text" name="schoolid" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Birthdate</label>
                    <input type="date" name="birthdate" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <div class="text-center mt-3">
                <small>
                    No account yet? 
                    <a href="sign_up.php" style="color: #0d6efd; text-decoration: underline;">Sign up here</a>.<br>
                    Or log in as guest 
                    <a href="../guest/guest_log.php" style="color: #0d6efd; text-decoration: underline;">here</a>.
                </small>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
