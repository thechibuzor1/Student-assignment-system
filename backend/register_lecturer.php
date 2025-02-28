<?php
include_once('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($connection, trim($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = mysqli_real_escape_string($connection, trim($_POST['password']));
    $cpassword = mysqli_real_escape_string($connection, trim($_POST['cpassword']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    if ($password !== $cpassword) {
        die("Passwords do not match");
    }

    // Check if email already exists in student, lecturer, or admin tables
    $check_email_query = "SELECT email FROM student WHERE email = '$email' 
                          UNION 
                          SELECT email FROM lecturer WHERE email = '$email' 
                          UNION 
                          SELECT email FROM admin WHERE email = '$email'";

    $result = mysqli_query($connection, $check_email_query);
    
    if (mysqli_num_rows($result) > 0) {
        die("This email is already registered.");
    }

    // Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert lecturer into database
    $query = "INSERT INTO lecturer (name, email, password) VALUES ('$name', '$email', '$hashed_password')";

    if (mysqli_query($connection, $query)) {

        // Clear any existing session (for student, lecturer, or admin)
        session_unset(); 
        session_destroy(); 
        session_start(); 

        // Set new lecturer session
        $_SESSION['lecturer_name'] = $name;
        $_SESSION['lecturer_email'] = $email;

        // Redirect to lecturer dashboard
        header('Location: ../views/lecturer.php');
        
        exit();
    } else {
        echo "Error: " . mysqli_error($connection);
    }
}
?>
