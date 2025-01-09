<?php



include("./config.php");

if (isset($_GET['deleteid'])) {
    $id = $_GET['deleteid'];


    $sql = $conn->prepare("DELETE FROM `students` WHERE id = ?");


    $sql->bind_param('i', $id);


    $result = $sql->execute();

    if ($result) {

        header('location:students.php');
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

$conn->close();
?>