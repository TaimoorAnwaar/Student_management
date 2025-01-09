<?php

session_start();
include("navbar.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("./config.php");

$students_per_page = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = max($page, 1);
$offset = ($page - 1) * $students_per_page;

$programs_query = "SELECT program_id, program_name FROM program";
$programs_result = $conn->query($programs_query);

$filter_program = isset($_GET['program_id']) ? intval($_GET['program_id']) : '';
$filter_fee_status = isset($_GET['fee_status']) ? $_GET['fee_status'] : '';

// Prepare conditions for count query
$count_query = "SELECT COUNT(*) AS total_students FROM students";
$conditions = [];

if (!empty($filter_program)) {
    $conditions[] = "students.program_id = $filter_program";  // Explicitly reference students.program_id
}
if (!empty($filter_fee_status)) {
    $conditions[] = "students.fee_status = '" . $conn->real_escape_string($filter_fee_status) . "'"; // Specify students.fee_status
}
if (!empty($conditions)) {
    $count_query .= " WHERE " . implode(' AND ', $conditions);
}

$total_result = $conn->query($count_query);
$total_students = $total_result->fetch_assoc()['total_students'];
$total_pages = ceil($total_students / $students_per_page);

// Prepare main query with conditions and pagination
$sql = "SELECT students.*, program.program_name, program.program_fee 
        FROM students 
        LEFT JOIN program ON students.program_id = program.program_id";

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " LIMIT $students_per_page OFFSET $offset"; // Apply pagination

$students_result = $conn->query($sql);
if (!$students_result) {
    die("Error executing query: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Make footer stick to the bottom */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content {
            flex: 1;
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="container my-5">
            <div class="d-flex justify-content-between align-items-center ">
                <h2 class="text-center w-100">Students List</h2>
            </div>

            <!-- Filter Form -->
            <form method="GET" class="d-flex">
                <select name="program_id" class="form-select me-2" onchange="this.form.submit()">
                    <option value="">All Programs</option>
                    <?php
                    if ($programs_result->num_rows > 0) {
                        while ($program = $programs_result->fetch_assoc()) {
                            $selected = ($filter_program == $program['program_id']) ? 'selected' : '';
                            echo '<option value="' . $program['program_id'] . '" ' . $selected . '>' . htmlspecialchars($program['program_name']) . '</option>';
                        }
                    }
                    ?>
                </select>
                <select name="fee_status" class="form-select me-2" onchange="this.form.submit()">
                    <option value="">All Fees</option>
                    <option value="paid" <?php echo ($filter_fee_status == 'paid') ? 'selected' : ''; ?>>Paid</option>
                    <option value="unpaid" <?php echo ($filter_fee_status == 'unpaid') ? 'selected' : ''; ?>>Unpaid
                    </option>
                </select>
            </form>

            <div class="d-flex justify-content-between my-3">
                <a href="createstudent.php" class="btn btn-primary text-light">Add Student</a>
            </div>

            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Sr #</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Mobile Number</th>
                        <th scope="col">Gender</th>
                        <th scope="col">Semester</th>
                        <th scope="col">Program</th>
                        <th scope="col">Fees</th>
                        <th scope="col">Fee Status</th>
                        <th scope="col">Details</th>
                        <th scope="col">Operations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $students = $students_result->fetch_all(MYSQLI_ASSOC);
                    $counter = $offset + 1;

                    if (!empty($students)) {
                        foreach ($students as $row) {
                            echo '<tr>
                                <th scope="row">' . $counter . '</th> 
                                <td>' . htmlspecialchars($row['name']) . '</td>
                                <td>' . htmlspecialchars($row['email']) . '</td>
                                <td>' . htmlspecialchars($row['mobile']) . '</td>
                                <td>' . htmlspecialchars($row['gender']) . '</td>
                                <td>' . htmlspecialchars($row['smester']) . '</td>
                                <td>' . (!empty($row['program_name']) ? htmlspecialchars($row['program_name']) : 'N/A') . '</td>
                                <td>' . (!empty($row['program_fee']) ? htmlspecialchars($row['program_fee']) : 'N/A') . '</td>
                                <td ' . ($row['fee_status']) . '; color: white;">
                                    ' . htmlspecialchars($row['fee_status']) . '
                                </td>
                                <td>
                                    <a href="detail.php?detailid=' . $row['id'] . '" class="btn btn-link btn-sm">Detail</a>
                                </td>
                                <td>
                                    <a href="update.php?updateid=' . $row['id'] . '" class="btn btn-primary btn-sm">Update</a>
                                    <a href="delete.php?deleteid=' . $row['id'] . '" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>';
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='11' class='text-center'>No students found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?page=<?php echo $page - 1; ?>&program_id=<?php echo $filter_program; ?>&fee_status=<?php echo $filter_fee_status; ?>">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link"
                                href="?page=<?php echo $i; ?>&program_id=<?php echo $filter_program; ?>&fee_status=<?php echo $filter_fee_status; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?page=<?php echo $page + 1; ?>&program_id=<?php echo $filter_program; ?>&fee_status=<?php echo $filter_fee_status; ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Footer -->
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