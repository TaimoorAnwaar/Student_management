<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .signup-container {
            max-width: 500px;
            margin: 130px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .signup-container h1 {
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

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="signup-container">
        <?php
        include("./config.php");
        if (isset($_POST["submit"])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $msg = 'Password does not match the confirm password';

            if ($password !== $confirm_password) {
                echo '<div class="alert alert-danger text-center">' . $msg . '</div>';
            } else {
                $emailCheckQuery = "SELECT * FROM users WHERE email = ?";
                $stmt = $conn->prepare($emailCheckQuery);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo '<div class="alert alert-danger text-center">Email is already registered. Please use a different email.</div>';
                } else {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sss", $name, $email, $passwordHash);

                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success text-center">Registration successful! Data stored in database.</div>';
                        header('location:login.php');
                    } else {
                        echo '<div class="alert alert-danger text-center">Error: ' . $conn->error . '</div>';
                    }
                    $stmt->close();
                }
            }
        }
        ?>
        <h1>Signup</h1>
        <form action="signup.php" method="post" class="my-4">
            <div class="form-group">
                <input type="text" class="form-control" name="name" placeholder="Enter your name" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm your password"
                    required>
            </div>
            <button name="submit" type="submit" class="btn btn-primary btn-block">Signup</button>
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>

</body>

</html>