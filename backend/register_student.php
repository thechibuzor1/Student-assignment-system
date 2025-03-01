<?php
include_once('config.php');
session_start(); // Start session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric_no = mysqli_real_escape_string($connection, trim($_POST['matric_no']));
    $name = mysqli_real_escape_string($connection, trim($_POST['name']));
    $level = mysqli_real_escape_string($connection, trim($_POST['level']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $cgpa = mysqli_real_escape_string($connection, trim($_POST['cgpa']));
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

    // Insert student into database
    $query = "INSERT INTO student (matricNumber, name, level, email, cgpa, password) 
              VALUES ('$matric_no', '$name', '$level', '$email', '$cgpa', '$hashed_password')";

    if (mysqli_query($connection, $query)) {

        // Reset session properly
        $_SESSION = [];
        session_regenerate_id(true);

        // Set student session
        $_SESSION['user_id'] = $matric_no; // Ensuring consistent session key
        $_SESSION['matric_no'] = $matric_no;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = "student";

        header('Location: ../views/student.php');
        exit();

    } else {
        echo "Error: " . mysqli_error($connection);
    }
}
?>
