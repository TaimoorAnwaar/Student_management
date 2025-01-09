<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
   
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Page Title -->
        
            <span class="navbar-text me-3">
                Welcome to <?php echo basename($_SERVER['PHP_SELF'], '.php'); ?> Page
            </span>

            <!-- Navbar Title -->
           
            <a class="navbar-brand mx-auto text-center">Student Management System</a>

            <!-- Dropdown Menu -->
                <div class="dropdown">
                    <div  class="btn  text-white mt-2"  id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Menu
</div>
                    <ul class="dropdown-menu dropdown-menu-start"  aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="Students.php">Home</a></li>
                        <li><a class="dropdown-item " href="attendance.php">Attendance</a></li>
                        <li><a class="dropdown-item " href="attendanceview.php">View Attendance</a></li>
                        <div class="dropdown-divider"></div>

                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </div>
        </div>
    </nav>

    <!-- Bootstrap Bundle with Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
