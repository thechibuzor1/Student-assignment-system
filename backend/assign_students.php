<?php
include_once('config.php');
session_start();

// Ensure only admin can run the assignment
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch students sorted by CGPA in descending order
$students_query = mysqli_query($connection, "SELECT * FROM student ORDER BY cgpa DESC");
$students = [];
while ($student = mysqli_fetch_assoc($students_query)) {
    $students[] = $student;
}

// Fetch lecturers sorted by rank in descending order
$lecturers_query = mysqli_query($connection, "SELECT * FROM lecturer ORDER BY rank DESC");
$lecturers = [];
while ($lecturer = mysqli_fetch_assoc($lecturers_query)) {
    $lecturers[] = $lecturer;
}

$total_lecturers = count($lecturers);
if ($total_lecturers === 0) {
    echo "No lecturers available for assignment.";
    exit();
}

$index = 0; // Track lecturer index

foreach ($students as $student) {
    $lecturer = $lecturers[$index % $total_lecturers]; // Assign based on round-robin with sorted ranking
    $supervisorID = $lecturer['id'];
    $matricNumber = $student['matricNumber'];

    // Assign student to lecturer
    $updateQuery = "UPDATE student SET supervisorID = '$supervisorID' WHERE matricNumber = '$matricNumber'";
    mysqli_query($connection, $updateQuery);

    $index++; // Move to the next lecturer in a cyclic manner
}

echo "Assignment completed successfully.";
?>
