<?php
include("config.php");
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = "";
$success = "";

// Fetch programs for the dropdown
$programs_query = "SELECT program_id, program_name FROM program";
$programs_result = $conn->query($programs_query);

$attendance_records = [];
$program_name = ""; // Initialize the program_name variable

if (isset($_POST['program_id']) && isset($_POST['date'])) {
    $program_id = $_POST['program_id'];
    $date = $_POST['date'];

    // Fetch program name based on selected program_id
    $program_name_query = $conn->prepare("SELECT program_name FROM program WHERE program_id = ?");
    $program_name_query->bind_param('i', $program_id);
    $program_name_query->execute();
    $program_name_result = $program_name_query->get_result();

    if ($program_name_result->num_rows > 0) {
        $program_name_row = $program_name_result->fetch_assoc();
        $program_name = $program_name_row['program_name']; // Assign program name
    }

    // Fetch attendance records for the selected program and date
    $attendance_query = $conn->prepare("SELECT a.student_id, a.status, s.name 
                                       FROM attendance a
                                       JOIN students s ON a.student_id = s.id
                                       WHERE a.date = ? AND s.program_id = ?");
    $attendance_query->bind_param('si', $date, $program_id);
    $attendance_query->execute();
    $attendance_result = $attendance_query->get_result();

    while ($row = $attendance_result->fetch_assoc()) {
        $attendance_records[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include("navbar.php"); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">View Attendance</h1>

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
                            <option value="<?php echo $program['program_id']; ?>">
                                <?php echo $program['program_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">View Attendance</button>
                </div>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <br>
        <br>

        <?php if (!empty($attendance_records)): ?>
            <h3 class="text-center">Attendance for Program: <?php echo htmlspecialchars($program_name); ?> on
                <?php echo htmlspecialchars($date); ?></h3>
            <br>
            <br>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_records as $record): ?>
                        <tr>
                            <td><?php echo $record['name']; ?></td>
                            <td><?php echo $record['status']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (isset($program_id) && isset($date)): ?>
            <div class="alert alert-warning">No attendance records found for the selected date and program.</div>
        <?php endif; ?>
    </div>
 
 
    <footer class="bg-dark text-light text-center py-3">
        <div class="container">
            © <?php echo date('Y'); ?> - All Rights Reserved.
        </div>
    </footer>
</body>

</html> 