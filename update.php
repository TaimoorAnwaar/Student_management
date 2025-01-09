<?php
include("./config.php");
session_start();
include("navbar.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit;
}

if (isset($_GET['updateid'])) {
    $id = $_GET['updateid'];

    $sql = $conn->prepare("SELECT * FROM `students` WHERE id = ?");
    $sql->bind_param('i', $id);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo "Record not found!";
        exit;
    }
} else {
    echo "No ID provided!";
    exit;
}

$programs_query = "SELECT program_id, program_name FROM program";
$programs_result = $conn->query($programs_query);

$error = "";

if (isset($_POST["submit"])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $gender = $_POST['gender'];
    $semester = $_POST['semester'];
    $program_id = $_POST['program_id'];
    $fee_status = $_POST['fee_status']; // Capture fee status
    $image = $_FILES['image']['name'];
    $updatedImage = $row['image'] ?? ''; // Fix for undefined image key

    $name = $first_name . ' ' . $last_name;

    if (empty($first_name) || empty($last_name) || empty($email) || empty($mobile) || empty($gender) || empty($semester) || empty($program_id) || empty($fee_status)) {
        $error = "All fields are required.";
    } elseif (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);

        // Check for file upload errors
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $updatedImage = $image; // If a new image is uploaded, update $updatedImage
        } else {
            $error = "Error uploading image.";
        }
    }

    if (empty($error)) {
        $sql = $conn->prepare("UPDATE `students` SET name = ?, email = ?, mobile = ?, gender = ?, smester = ?, program_id = ?, fee_status = ?, image = ? WHERE id = ?");
        $sql->bind_param('sssssissi', $name, $email, $mobile, $gender, $semester, $program_id, $fee_status, $updatedImage, $id);

        if ($sql->execute()) {
            header('location: Students.php');
            exit();
        } else {
            $error = "Update failed: " . $conn->error;
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Student Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        <h2>Update Student Record</h2>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="post" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col">
                    <label>First Name</label>
                    <input type="text" class="form-control" placeholder="First name" name="first_name"
                        value="<?php echo htmlspecialchars(explode(' ', $row['name'])[0]); ?>" required>
                </div>
                <div class="col">
                    <label>Last Name</label>
                    <input type="text" class="form-control" placeholder="Last name" name="last_name"
                        value="<?php echo htmlspecialchars(explode(' ', $row['name'])[1]); ?>" required>
                </div>
            </div>

            <div class="my-3">
                <label>Email</label>
                <input type="email" class="form-control" placeholder="Enter Your Email" name="email"
                    value="<?php echo htmlspecialchars($row['email']); ?>" required>
            </div>

            <div class="my-3">
                <label>Phone Number</label>
                <input type="number" class="form-control" placeholder="Enter Your Mobile Number" name="mobile"
                    value="<?php echo htmlspecialchars($row['mobile']); ?>" required>
            </div>

            <div class="my-3">
                <label for="gender">Gender</label>
                <select id="gender" class="form-select" name="gender" required>
                    <option value="" disabled>Choose your gender</option>
                    <option value="Male" <?php if ($row['gender'] == 'Male')
                        echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($row['gender'] == 'Female')
                        echo 'selected'; ?>>Female</option>
                </select>
            </div>

            <div class="my-3">
                <label>Semester</label>
                <input type="number" class="form-control" placeholder="Enter Your Semester" name="semester"
                    value="<?php echo htmlspecialchars($row['smester']); ?>" required>
            </div>

            <div class="my-3">
                <label for="program">Program</label>
                <select id="program" class="form-select" name="program_id" required>
                    <option value="" disabled>Choose your Program</option>
                    <?php
                    if ($programs_result->num_rows > 0) {
                        while ($program = $programs_result->fetch_assoc()) {
                            $selected = ($row['program_id'] == $program['program_id']) ? 'selected' : '';
                            echo '<option value="' . $program['program_id'] . '" ' . $selected . '>' . htmlspecialchars($program['program_name']) . '</option>';
                        }
                    } else {
                        echo '<option value="" disabled>No programs available</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="my-3">
                <label for="fee_status">Fee Status</label>
                <select id="fee_status" class="form-select" name="fee_status" required>
                    <option value="" disabled>Choose Fee Status</option>
                    <option value="paid" <?php if ($row['fee_status'] == 'paid')
                        echo 'selected'; ?>>Paid</option>
                    <option value="unpaid" <?php if ($row['fee_status'] == 'unpaid')
                        echo 'selected'; ?>>Unpaid</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Product Image</label>
                <input type="file" class="form-control" name="image">
                <small>Current image: <?php echo htmlspecialchars($row['image']); ?></small>
                <small><img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Current Image"
                        width="100"></small>
            </div>

            <button type="submit" class="btn btn-primary" name="submit">Update</button>
        </form>
    </div>
    <footer class="bg-dark text-light text-center py-3 sticky-bottom">
        <div class="container">
            © <?php echo date('Y'); ?> - All Rights Reserved.
        </div>
    </footer>
</body>

</html>