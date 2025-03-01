<?php
include_once('config.php');
session_start();

// Ensure only admin can run the assignment
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch students sorted by CGPA in descending order
$students = mysqli_query($connection, "SELECT * FROM student ORDER BY cgpa DESC");

// Fetch lecturers sorted by rank in descending order
$lecturers = mysqli_query($connection, "SELECT * FROM lecturer ORDER BY rank DESC");

$lecturers_array = [];
while ($lecturer = mysqli_fetch_assoc($lecturers)) {
    $lecturers_array[] = $lecturer;
}

$total_lecturers = count($lecturers_array);
if ($total_lecturers === 0) {
    echo "No lecturers available for assignment.";
    exit();
}

$index = 0; // Track lecturer index

while ($student = mysqli_fetch_assoc($students)) {
    $lecturer = $lecturers_array[$index % $total_lecturers]; // Round-robin selection
    $supervisorID = $lecturer['id']; // Assign lecturer ID
    $matricNumber = $student['matricNumber'];
    
    // Assign student to lecturer
    $updateQuery = "UPDATE student SET supervisorID = '$supervisorID' WHERE matricNumber = '$matricNumber'";
    mysqli_query($connection, $updateQuery);
    
    $index++; // Move to the next lecturer in a cyclic manner
}

echo "Assignment completed";
?>
