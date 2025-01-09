<?php
session_start();


if (isset($_SESSION['user_id'])) {
    header("Location: students.php");
    exit;
}

include("./config.php");

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo "<script>alert('All fields are required!');</script>";
    } else {
        $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];

                // Redirect to the student page
                header("Location: students.php");
                exit;
            } else {
                echo "<script>alert('Invalid credentials!');</script>";
            }
        } else {
            echo "<script>alert('Invalid credentials!');</script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            max-width: 400px;
            margin: 120px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .login-container h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-control {
            border-radius: 30px;
        }

        .btn-primary {
            border-radius: 30px;
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
        }

        .signup-link a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>Login</h1>
        <form action="login.php" method="post">
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email address" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button name="submit" type="submit" class="btn btn-primary btn-block">Login</button>
            <div class="signup-link">
                <p>Don't have an account? <a href="signup.php">Signup</a></p>
            </div>
        </form>
    </div>
</body>

</html>