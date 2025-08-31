<?php
include '../../../database/db_connect.php';

// Handle form submission
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
        // Redirect to prevent resubmission
        header("Location: logIn.php");
        exit;
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

// Check for success parameter in URL
if (isset($_GET['success'])) {
    $success = "Account created successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>Sign Up</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($success)) { ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php } ?>
                        <?php if(isset($error)) { ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php } ?>
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
                                    <?php foreach($programs as $prog) { ?>
                                        <option value="<?php echo htmlspecialchars($prog); ?>"><?php echo htmlspecialchars($prog); ?></option>
                                    <?php } ?>
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
                            Already have an Account? Click 
                            <a href="logIn.php" style="color: #0d6efd; text-decoration: underline;">here</a>.<br>
                        </small>
                        </div>
                    </div>
                    <div class="card-footer text-center text-muted">
                        &copy; <?php echo date('Y'); ?> Your School
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
