<?php
include("config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['program_id'])) {
    $program_id = $_POST['program_id'];

    // Debugging: Check if program_id is being passed correctly
    if (empty($program_id)) {
        echo json_encode(['error' => 'Program ID is missing.']);
        exit;
    }

    // Query to fetch students for the given program_id
    $students_query = $conn->prepare("SELECT id, name FROM students WHERE program_id = ?");
    $students_query->bind_param('i', $program_id);
    $students_query->execute();
    $students_result = $students_query->get_result();

    $students = [];
    while ($row = $students_result->fetch_assoc()) {
        $students[] = $row;
    }

    // Return the students as JSON
    if (!empty($students)) {
        echo json_encode(['students' => $students]);
    } else {
        echo json_encode(['students' => []]);
    }
}
?>