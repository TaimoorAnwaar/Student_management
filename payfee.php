<?php
include("./config.php");
session_start();
include('./navbar.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if fee ID is provided
if (isset($_GET['feeid'])) {
    $feeid = $_GET['feeid'];

    // Fetch student data
    $sql = $conn->prepare("SELECT students.*, program.program_name, program.program_fee, students.fee_deadline 
                           FROM students 
                           LEFT JOIN program ON students.program_id = program.program_id 
                           WHERE students.id = ?");
    $sql->bind_param('i', $feeid);
    $sql->execute();
    $result = $sql->get_result();
    $student = $result->fetch_assoc();

    if (!$student) {
        echo "Record not found!";
        exit;
    }

    // Calculate fine if deadline is exceeded
    $today = new DateTime();
    $deadline = new DateTime($student['fee_deadline']);
    $fine = 0;

    if ($today > $deadline) {
        $days_late = $today->diff($deadline)->days;
        $fine = $days_late * 30;
    }

    // Handle fee submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $update_sql = $conn->prepare("UPDATE students SET fee_status = 'Paid' WHERE id = ?");
        $update_sql->bind_param('i', $feeid);
        $update_sql->execute();

        if ($update_sql->affected_rows > 0) { 
            echo "<script>alert('Fee submitted successfully!');</script>";
            header("Location: students.php");
            exit;
        } else {
            echo "<script>alert('Failed to update fee status!');</script>";
        }
    }
} else {
    echo "No Fee ID provided!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Fee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table th {
            text-align: left;
        }

        .table td {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="card">
            <div class="card-header text-center bg-primary text-white">
                <h3>Fee Slip</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?php echo htmlspecialchars($student['mobile']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Program</th>
                        <td><?php echo htmlspecialchars($student['program_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Semester</th>
                        <td><?php echo htmlspecialchars($student['smester']); ?></td>
                    </tr>
                    <tr>
                        <th>Fee Deadline</th>
                        <td><?php echo htmlspecialchars($student['fee_deadline']); ?></td>
                    </tr>
                    <tr>
                        <th>Total Fee</th>
                        <td><?php echo htmlspecialchars($student['program_fee']); ?> PKR</td>
                    </tr>
                    <tr>
                        <th>Fine</th>
                        <td><?php echo $fine; ?> PKR</td>
                    </tr>
                    <tr>
                        <th>Grand Total</th>
                        <td><?php echo $student['program_fee'] + $fine; ?> PKR</td>
                    </tr>
                    <tr>
                        <th>Fee Status</th>
                        <td><?php echo htmlspecialchars($student['fee_status']); ?></td>
                    </tr>
                </table>
            </div>
            <div class="card-footer text-center">
               
                    <form method="POST">
                        <button type="submit" class="btn btn-success"> Submit Fee</button>
                    </form>
                
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="students.php" class="btn btn-secondary">Back to Student page</a>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>
