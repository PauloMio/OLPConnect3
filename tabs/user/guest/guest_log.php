<?php
include '../../../database/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $conn->real_escape_string($_POST['name']);
    $school   = $conn->real_escape_string($_POST['school']);
    $id_num   = $conn->real_escape_string($_POST['id_num']);
    $course   = $conn->real_escape_string($_POST['course']);
    $purpose  = $conn->real_escape_string($_POST['purpose']);

    $sql = "INSERT INTO guestlog (name, school, id_num, course, purpose, created_at, updated_at) 
            VALUES ('$name', '$school', '$id_num', '$course', '$purpose', NOW(), NOW())";

    if ($conn->query($sql) === TRUE) {
        header("Location: ebook_collection.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Guest Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: #f8f9fa;
    }
    main {
      flex: 1; /* pushes footer down */
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
  </style>
</head>
<body>

<main>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg p-4 rounded-3">
          <h3 class="text-center mb-4">Guest Login</h3>
          <form method="POST">
            <!-- Name -->
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <textarea class="form-control" id="name" name="name" rows="2" required></textarea>
            </div>

            <!-- School -->
            <div class="mb-3">
              <label for="school" class="form-label">School</label>
              <textarea class="form-control" id="school" name="school" rows="2"></textarea>
            </div>

            <!-- ID Number -->
            <div class="mb-3">
              <label for="id_num" class="form-label">ID Number</label>
              <input type="text" class="form-control" id="id_num" name="id_num">
            </div>

            <!-- Course -->
            <div class="mb-3">
              <label for="course" class="form-label">Course</label>
              <input type="text" class="form-control" id="course" name="course">
            </div>

            <!-- Purpose -->
            <div class="mb-3">
              <label for="purpose" class="form-label">Purpose</label>
              <textarea class="form-control" id="purpose" name="purpose" rows="2" required></textarea>
            </div>

            <!-- Submit Button -->
            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg">Login as Guest</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
