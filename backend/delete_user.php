<?php
require 'config.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $role = $_POST['role']; // Expecting "lecturer" or "student"

    if ($role === "lecturer") {
        $query = "DELETE FROM lecturer WHERE email = ?";
    } elseif ($role === "student") {
        $query = "DELETE FROM student WHERE email = ?";
    } else {
        die("Invalid role specified.");
    }

    // Prepare and execute the query
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../views/dashboard.php?success=User deleted");
        exit();
    } else {
        die("Error deleting user: " . mysqli_error($connection));
    }
}
?>
