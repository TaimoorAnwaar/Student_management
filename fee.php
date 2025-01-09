
<?php
include("./config.php");
session_start();
include("./navbar.php");


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];


    $sql = $conn->prepare("SELECT students.*, program.program_name, program.program_fee FROM students 
                           LEFT JOIN program ON students.program_id = program.program_id 
                           WHERE students.id = ?");
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
    <title>Fee Bill</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table th {
            width: 40%;
            text-align: left;
            /* Aligning table headings to the left */
        }

        .table td {
            width: 60%;
        }

        .student-image {
            border: 4px solid #007bff;
            padding: 5px;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="card">
            <!-- Card Header -->
            <div class="card-header text-center bg-primary text-white">
                <h3 class="mb-0">Fee Bill</h3>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <!-- Student Image -->
                <div class="text-center mb-4">
                    <img src="uploads/<?php echo htmlspecialchars($student['image']); ?>" alt="Student Image"
                        class="img-fluid rounded-circle student-image"
                        style="width: 150px; height: 150px; object-fit: cover;">
                </div>

                <!-- Fee Bill Table -->
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Phone Number</th>
                        <td><?php echo htmlspecialchars($student['mobile']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
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
                        <th>Total Fees</th>
                        <td><?php echo htmlspecialchars($student['program_fee']); ?></td>
                    </tr>
                    <tr>
                        <th>Want to Submit your fees ?</th>
                        <td><div>
            <a href="payfee.php?feeid=<?php echo htmlspecialchars($student['id']); ?>"
                >Submit fee</a>
        </div></td>
                    </tr>


                </table>
            </div>

            <!-- Card Footer -->
            <div class="card-footer text-center bg-light">
                <p class="mb-0">Thank you for being a part of our institution!</p>
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-center mt-4">
            <a href="detail.php?detailid=<?php echo htmlspecialchars($student['id']); ?>"
                class="btn btn-secondary px-4">Back to Student Detail</a>
        </div>

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