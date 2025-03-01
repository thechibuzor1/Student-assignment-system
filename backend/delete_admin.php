<?php
include_once('config.php');
session_start();

// Ensure only admins can perform this action
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Unauthorized action.";
    exit();
}

// Validate email
if (!isset($_POST['email']) || empty($_POST['email'])) {
    echo "Invalid request.";
    exit();
}

$email = mysqli_real_escape_string($connection, $_POST['email']);

// Prevent an admin from deleting themselves
if ($_SESSION['email'] === $email) {
    echo "You cannot delete yourself!";
    exit();
}

// Delete admin from the database
$query = "DELETE FROM admin WHERE email = '$email' ";
if (mysqli_query($connection, $query)) {
    header("Location: ../views/dashboard.php?success=Admin deleted");
} else {
    echo "Error deleting admin: " . mysqli_error($connection);
}
?>
