<?php
include("./config.php");
session_start();
include("./navbar.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit;
}

if (isset($_GET['detailid'])) {
    $id = $_GET['detailid'];

    // Fetch student details based on the ID
    $sql = $conn->prepare("SELECT students.*, program.program_name FROM students LEFT JOIN program ON students.program_id = program.program_id WHERE students.id = ?");
    $sql->bind_param('i', $id);
    $sql->execute();
    $result = $sql->get_result();
    $student = $result->fetch_assoc();

    if (!$student) {
        echo "Record not found!";
        exit;
    }
} else {
    echo "No ID provided!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        <h2 class="text-center">Student Detail</h2>

        <!-- Student Image -->
        <div class="text-center mb-4">
            <img src="uploads/<?php echo $student['image']; ?>" alt="Student image" class="rounded-circle"
                style="width: 150px; height: 150px; object-fit: cover;">
        </div>

        <!-- Student Details Table -->
        <table class="table table-bordered">
            <tr>
                <th>Name</th>
                <td><?php echo htmlspecialchars($student['name']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($student['email']); ?></td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td><?php echo htmlspecialchars($student['mobile']); ?></td>
            </tr>
            <tr>
                <th>Gender</th>
                <td><?php echo htmlspecialchars($student['gender']); ?></td>
            </tr>
            <tr>
                <th>Semester</th>
                <td><?php echo htmlspecialchars($student['smester']); ?></td>
            </tr>
            <tr>
                <th>Program</th>
                <td><?php echo htmlspecialchars($student['program_name']); ?></td>
            </tr>
            <tr>
                <th>Fees status</th>
                <td><?php echo htmlspecialchars($student['fee_status']); ?></td>
            </tr>
            <?php if ($student['fee_status'] === 'unpaid'): ?>
                <tr>
                    <th>Fees Bill</th>
                    <td>
                        <a href="fee.php?id=<?php echo $student['id']; ?>" class="btn btn-link btn-sm">Show Fee Bill</a>
                    </td>
                </tr>
            <?php endif; ?>

        </table>

        <a href="Students.php" class="btn btn-secondary">Back to Students List</a>
    </div>
    <footer class="bg-dark text-light text-center py-3">
        <div class="container">
            © <?php echo date('Y'); ?> - All Rights Reserved.
        </div>
    </footer>

</body>

</html>


<?php
$conn->close();
?>