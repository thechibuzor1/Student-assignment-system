<?php
include_once('config.php');
session_start(); // Start session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    if ($password !== $cpassword) {
        die("Passwords do not match");
    }

    // Check if email already exists in student, lecturer, or admin tables
    $check_email_query = "SELECT email FROM student WHERE email = ? 
                          UNION 
                          SELECT email FROM lecturer WHERE email = ? 
                          UNION 
                          SELECT email FROM admin WHERE email = ?";
    
    if ($stmt = mysqli_prepare($connection, $check_email_query)) {
        mysqli_stmt_bind_param($stmt, "sss", $email, $email, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            die("This email is already registered.");
        }
        mysqli_stmt_close($stmt);
    } else {
        die("Database query error.");
    }

    // Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert lecturer into the database using prepared statements
    $insert_query = "INSERT INTO lecturer (name, email, password) VALUES (?, ?, ?)";

    if ($stmt = mysqli_prepare($connection, $insert_query)) {
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);
        if (mysqli_stmt_execute($stmt)) {

            // Clear any existing session (for student, lecturer, or admin)
            session_unset();
            session_destroy();
            session_start();

            // Set new lecturer session
            $_SESSION['lecturer_id'] = mysqli_insert_id($connection); // Store lecturer ID
            $_SESSION['lecturer_name'] = $name;
            $_SESSION['lecturer_email'] = $email;

            // Redirect to lecturer dashboard
            header('Location: ../views/lecturer.php');
            exit();
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Database query error!";
    }
}

mysqli_close($connection);
?>
