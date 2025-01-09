

<?php
include("config.php");
session_start();

include("navbar.php");
// Check if user is logged in
if(!isset($_SESSION['user_id'])){

    header('Location:login.php');
}


// Fetch programs for the dropdown
$programs_query = "SELECT program_id, program_name FROM program";
$programs_result = $conn->query($programs_query);

$error = "";
$success = "";

// Fetch students for the selected program and date
$students = [];
if (isset($_POST['program_id']) && isset($_POST['date'])) {
    $program_id = $_POST['program_id'];
    $date = $_POST['date'];

    // Fetch students based on the selected program
    $students_query = $conn->prepare("SELECT id, name FROM students WHERE program_id = ?");
    $students_query->bind_param('i', $program_id);
    $students_query->execute();
    $students_result = $students_query->get_result();
    while ($row = $students_result->fetch_assoc()) {
        $students[] = $row;
    }

    // Handle attendance submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance'])) {
        $attendance = $_POST['attendance'] ?? [];

        if (empty($attendance)) {
            $error = "Please select attendance for all students.";
        } else {
            foreach ($attendance as $student_id => $status) {
                $sql = $conn->prepare("INSERT INTO attendance (student_id, date, status)
                                       VALUES (?, ?, ?)
                                       ON DUPLICATE KEY UPDATE status = VALUES(status)");
                $sql->bind_param('iss', $student_id, $date, $status);
                $sql->execute();
            }
            $success = "Attendance recorded successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>


    <div class="container mt-5">
        <h1 class="text-center mb-4">Mark Attendance</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Attendance Filter Form -->
        <form method="POST" class="mb-4">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label for="program_id" class="form-label">Program</label>
                    <select id="program_id" name="program_id" class="form-select" required>
                        <option value="">Select Program</option>
                        <?php while ($program = $programs_result->fetch_assoc()): ?>
                            <option value="<?php echo $program['program_id']; ?>" <?php if (isset($program_id) && $program_id == $program['program_id'])
                                   echo 'selected'; ?>>
                                <?php echo $program['program_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex justify-content-between align-items-end">
                    <button type="submit" class="btn btn-primary">Mark Attendance</button>
                    <!-- Adjusted the button design -->
                  
                </div>
            </div>
        </form>


        <!-- Students Table -->
        <?php if (!empty($students)): ?>
            <form method="POST" class="mb-4">
                <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
                <input type="hidden" name="program_id" value="<?php echo htmlspecialchars($program_id); ?>">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo $student['id']; ?></td>
                                <td><?php echo $student['name']; ?></td>
                                <td>
                                    <select name="attendance[<?php echo $student['id']; ?>]" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="Present">Present</option>
                                        <option value="Absent">Absent</option>
                                        <option value="Leave">Leave</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary"> Submit Attendance</button>
            </form>
        <?php elseif (isset($program_id) && isset($date)): ?>
            <div class="alert alert-warning">No students found for the selected program and date.</div>
        <?php endif; ?>
    </div>


    <footer class="bg-dark text-light text-center py-3">
        <div class="container">
            © <?php echo date('Y'); ?> - All Rights Reserved.
        </div>
    </footer>
</body>

</html>