<?php
include_once('config.php');
session_start(); // Start session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $role = $_POST['role']; // "student", "lecturer", or "admin"

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    // Select user from the correct table
    if ($role === "student") {
        $query = "SELECT * FROM student WHERE email = ?";
    } else if ($role === "lecturer") {
        $query = "SELECT * FROM lecturer WHERE email = ?";
    } else if ($role === "admin") {
        $query = "SELECT * FROM admin WHERE email = ?";
    } else {
        die("Invalid role selected");
    }

    if ($stmt = mysqli_prepare($connection, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Verify the password
            if (password_verify($password, $row['password'])) {
                // Clear any existing session (force logout of any logged-in user)
                session_unset();
                session_destroy();
                session_start();

                // Set new session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $role;

                if ($role === "student") {
                    $_SESSION['matric_no'] = $row['matricNumber'];
                    header("Location: ../views/student.php"); // Redirect student
                } else if ($role === "lecturer") {
                    header("Location: ../views/lecturer.php"); // Redirect lecturer
                } else if ($role === "admin") {
                    header("Location: ../views/dashboard.php"); // Redirect admin
                }
                exit();
            } else {
                echo "Invalid email or password!";
            }
        } else {
            echo "User not found!";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Database query error!";
    }
}

mysqli_close($connection);
?>
