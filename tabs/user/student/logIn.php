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
        // set status active
        $stmt = $conn->prepare("UPDATE account SET status='active', loggedin=NOW() WHERE id=?");
        $stmt->bind_param("i", $account['id']);
        $stmt->execute();
        $stmt->close();

        $_SESSION['account'] = $account; // store user info
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
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

  <div class="card p-4 shadow" style="max-width:400px; width:100%;">
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
  </div>

</body>
</html>
