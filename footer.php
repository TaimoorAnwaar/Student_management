<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .footer {
            padding: 19px 0;
            /* Adjusts padding for footer content */
        }
    </style>
</head>

<body>
    <!-- Footer -->
    <footer class="footer bg-dark sticky-bottom">
        <div class=" d-flex justify-content-center align-items-center">
            <span class="text-light">
                © <span id="year"></span> Copyright
            </span>
        </div>
    </footer>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <!-- JavaScript to Set Dynamic Year -->
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>

</html>