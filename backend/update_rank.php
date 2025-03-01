<?php
require 'config.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $rank = intval($_POST['rank']);

    // Ensure rank is strictly between 1 and 5
    if ($rank < 1 || $rank > 5) {
        die("Invalid rank! Rank must be between 1 and 5.");
    }

    // Update lecturer rank
    $query = "UPDATE lecturer SET rank = ? WHERE email = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "is", $rank, $email);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../views/dashboard.php?success=Rank updated");
        exit();
    } else {
        die("Error updating rank: " . mysqli_error($connection));
    }
}
?>
