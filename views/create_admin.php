<?php
include('header.php');
include_once('../backend/config.php');
session_start();

// Ensure only an admin can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'], $_POST['password'])) {
    $email = mysqli_real_escape_string($connection, trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Secure password hashing

    // Check if email already exists in students, lecturers, or users table
    $checkQuery = "SELECT email FROM student WHERE email = '$email' 
                   UNION 
                   SELECT email FROM lecturer WHERE email = '$email'
                   UNION
                   SELECT email FROM admin WHERE email = '$email'";
    $checkResult = mysqli_query($connection, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $message = "Error: Email is already in use by another user.";
    } else {
        // Insert new admin into admin table
        $query = "INSERT INTO admin (email, password) VALUES ('$email', '$password')";
        if (mysqli_query($connection, $query)) {
            $message = "Admin created successfully!";
        } else {
            $message = "Error: " . mysqli_error($connection);
        }
    }
}
?>

<div class="w-full max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-3xl mb-6 text-center text-gray-800">Create Admin</h2>

    <?php if (!empty($message)) : ?>
        <p class="text-center p-3 rounded mb-4 <?= strpos($message, 'Error') === false ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <form action="create_admin.php" method="POST" class="space-y-4">
        <div>
            <label class="block text-gray-700 font-medium mb-1">Email</label>
            <input type="email" name="email" required class="w-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3 rounded-lg outline-none">
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-1">Password</label>
            <input type="password" name="password" required class="w-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3 rounded-lg outline-none">
        </div>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 transition duration-300 text-white font-semibold py-3 rounded-lg">
            Create Admin
        </button>
    </form>
</div>
