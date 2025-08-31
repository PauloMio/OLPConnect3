<?php
include '../../../database/db_connect.php';

// Handle form submission
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $schoolid = $conn->real_escape_string($_POST['schoolid']);
    $program = $conn->real_escape_string($_POST['program']);
    $birthdate = $conn->real_escape_string($_POST['birthdate']);
    
    $stmt = $conn->prepare("INSERT INTO account (firstname, lastname, schoolid, program, birthdate, created_at, updated_at, status) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), 'active')");
    $stmt->bind_param("sssss", $firstname, $lastname, $schoolid, $program, $birthdate);
    
    if ($stmt->execute()) {
        $stmt->close();
        $success = "Account created successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
}

// Fetch programs for dropdown
$programs = [];
$result = $conn->query("SELECT program FROM program_user");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $programs[] = $row['program'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    display: flex;
    min-height: 100vh;
    margin: 0;
    font-family: Arial, sans-serif;
}

#main-content {
    flex-grow: 1;
    margin-left: 240px; /* width of sidebar */
    display: flex;
    flex-direction: column;
}

.sidebar.collapsed + #main-content {
    margin-left: 70px;
}

.signup-wrapper {
    flex: 1; /* take remaining vertical space */
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
}

.signup-card {
    max-width: 500px;
    width: 100%;
}
</style>
</head>
<body>

<!-- Sidebar -->
<?php include '../../../sidebar.php'; ?>

<!-- Main Content -->
<div id="main-content">
    <div class="signup-wrapper">
        <div class="card shadow signup-card">
            <div class="card-header text-center bg-primary text-white">
                <h4>Sign Up</h4>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" required>
                    </div>
                    <div class="mb-3">
                        <label for="schoolid" class="form-label">School ID</label>
                        <input type="text" class="form-control" id="schoolid" name="schoolid" required>
                    </div>
                    <div class="mb-3">
                        <label for="program" class="form-label">Program</label>
                        <select class="form-select" id="program" name="program" required>
                            <option value="" disabled selected>Select your program</option>
                            <?php foreach($programs as $prog): ?>
                                <option value="<?= htmlspecialchars($prog) ?>"><?= htmlspecialchars($prog) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="birthdate" class="form-label">Birthdate</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                </form>

                <div class="text-center mt-3">
                    <small>
                        Already have an account? 
                        <a href="logIn.php" style="color: #0d6efd; text-decoration: underline;">Log in here</a>.
                    </small>
                </div>
            </div>

            <!-- Footer inside card removed; using main footer below -->
        </div>
    </div>

    <!-- Main footer -->
    <?php include 'footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
