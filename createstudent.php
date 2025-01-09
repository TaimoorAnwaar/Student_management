<?php

include('./config.php');

session_start();
include('./navbar.php');


if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;

}


if (isset($_POST['submit'])) {
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $email = $_POST['email'];
  $mobile = $_POST['mobile'];
  $gender = $_POST['gender'];
  $semester = $_POST['Smester'];
  $program_id = $_POST['program_id'];
  $image = $_FILES['image']['name'];
  $status = $_POST['Fee_status'];
  $name = $first_name . ' ' . $last_name;

  if (empty($first_name) || empty($last_name) || empty($email) || empty($mobile) || empty($gender) || empty($program_id) || empty($semester) || empty($image) || empty($status)) {
    echo "<script>alert('All fields are required!');</script>";
    exit;
  }

  // Validate file upload
  if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo "<script>alert('File upload error. Please try again.');</script>";
    exit;
  }

  $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
  if (!in_array($_FILES['image']['type'], $allowed_types)) {
    echo "<script>alert('Only JPG, PNG, and GIF files are allowed.');</script>";
    exit;
  }

  if ($_FILES['image']['size'] > 2 * 1024 * 1024) { // Limit to 2MB
    echo "<script>alert('File size must be less than 2MB.');</script>";
    exit;
  }

  $target_dir = "uploads/";
  $target_file = $target_dir . basename($image);

  if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
    $sql = $conn->prepare("INSERT INTO students (name, email, smester, mobile, gender, program_id, image, Fee_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if ($sql === false) {
      echo "SQL preparation error: " . $conn->error;
      exit;
    }

    $sql->bind_param("ssississ", $name, $email, $semester, $mobile, $gender, $program_id, $image, $status);

    if ($sql->execute()) {
      header('Location: students.php');
      exit;
    } else {
      echo "Error executing query: " . $sql->error;
    }
  } else {
    echo "<script>alert('Failed to upload the image.');</script>";
  }

  $sql->close();
}

$conn->close();
?>



<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Student</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container my-5">
    <form method="post" enctype="multipart/form-data">
      <!-- Student Name-->
      <div class="row g-3">
        <div class="col">
          <label>First Name</label>
          <input type="text" class="form-control" placeholder="First name" name="first_name">
        </div>
        <div class="col">
          <label>Last Name</label>
          <input type="text" class="form-control" placeholder="Last name" name="last_name">
        </div>
      </div>

      <!-- Student Email -->
      <div class="my-3">
        <label>Email</label>
        <input type="email" class="form-control" placeholder="Enter Your Email" name="email">
      </div>

      <!-- Student Phone Number -->
      <div class="my-3">
        <label>Phone Number</label>
        <input type="number" class="form-control" placeholder="Enter Your Mobile Number" name="mobile">
      </div>

      <!-- Student Gender -->
      <div class="my-3">
        <label for="gender">Gender</label>
        <select id="gender" class="form-select" name="gender">
          <option value="" selected disabled>Choose your gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>
      <div class="my-3">
        <label for="program">Program</label>
        <select id="program" class="form-select" name="program_id" required>
          <option value="" selected disabled>Choose your Program</option>
          <?php
          // Fetch programs from the `program` table
          include("./config.php");

          $programs_query = "SELECT program_id, program_name FROM program";
          $programs_result = $conn->query($programs_query);

          if ($programs_result && $programs_result->num_rows > 0) {
            while ($program = $programs_result->fetch_assoc()) {
              echo '<option value="' . $program['program_id'] . '">' . htmlspecialchars($program['program_name']) . '</option>';
            }
          } else {
            echo '<option value="" disabled>No programs available</option>';
          }
          ?>
        </select>
      </div>



      <!-- Student Semester -->
      <div class="my-3">
        <label>Semester</label>
        <input type="number" class="form-control" placeholder="Enter Your Semester" name="Smester">
      </div>

      <!-- student Fee status-->
      <div class="my-3">
        <label for="fee_status">Fee Status</label>
        <select id="fee_status" class="form-select" name="Fee_status" required>
          <option value="" selected disabled>Is the Fees paid or not</option>
          <option value="paid">Paid</option>
          <option value="unpaid">Unpaid</option>
        </select>
      </div>


      <!-- Student Image -->
      <div class="my-3">
        <label for="image">Upload Image</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
      </div>


      <button type="submit" class="btn btn-primary" name="submit">Submit</button>
    </form>


  </div>
  <footer class="bg-dark text-light text-center py-3">
    <div class="container">
      © <?php echo date('Y'); ?> - All Rights Reserved.
    </div>
  </footer>
</body>

</html>