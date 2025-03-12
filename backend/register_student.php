<?php
include_once('config.php');
session_start(); // Start session to store errors

$_SESSION['error'] = ""; // Clear previous errors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric_no = mysqli_real_escape_string($connection, trim($_POST['matric_no']));
    $name = mysqli_real_escape_string($connection, trim($_POST['name']));
    $level = intval($_POST['level']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $cgpa = trim($_POST['cgpa']);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
    } elseif (!is_numeric($cgpa) || $cgpa < 0 || $cgpa > 5.0) {
        $_SESSION['error'] = "CGPA must be between 0.0 and 5.0.";
    } elseif (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters.";
    } elseif ($password !== $cpassword) {
        $_SESSION['error'] = "Passwords do not match.";
    } else {
        $check_email_query = "SELECT email FROM student WHERE email = '$email' 
                              UNION 
                              SELECT email FROM lecturer WHERE email = '$email' 
                              UNION 
                              SELECT email FROM admin WHERE email = '$email'";

        $result = mysqli_query($connection, $check_email_query);
        if (mysqli_num_rows($result) > 0) {
            $_SESSION['error'] = "This email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO student (matricNumber, name, level, email, cgpa, password) 
                      VALUES ('$matric_no', '$name', '$level', '$email', '$cgpa', '$hashed_password')";

            if (mysqli_query($connection, $query)) {
                session_unset();
                session_regenerate_id(true);
                $_SESSION['user_id'] = $matric_no;
                $_SESSION['matric_no'] = $matric_no;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = "student";
                header('Location: ../views/student.php');
                exit();
            } else {
                $_SESSION['error'] = "Database error: " . mysqli_error($connection);
            }
        }
    }
    header("Location: ../views/register.php"); // Redirect back to form to show error
    exit();
}
?>
