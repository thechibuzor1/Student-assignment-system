<?php
include('header.php');
include_once('../backend/config.php');

if (!isset($_SESSION['role']) || !isset($_SESSION['email'])) {
    header("Location: ../index.php");
    exit();
}

$role = $_SESSION['role'];
$email = $_SESSION['email'];
$message = "";

if ($role === "student") {
    $query = "SELECT name, matricNumber, level, cgpa, email FROM student WHERE email = ?";
} elseif ($role === "lecturer") {
    $query = "SELECT name, email FROM lecturer WHERE email = ?";
} elseif ($role === "admin") {
    $query = "SELECT email FROM admin WHERE email = ?";
} else {
    die("Invalid role detected.");
}

$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("User not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $newEmail = mysqli_real_escape_string($connection, trim($_POST['email']));
        $newPassword = !empty($_POST['password']) ? trim($_POST['password']) : null;
        $confirmPassword = !empty($_POST['confirm_password']) ? trim($_POST['confirm_password']) : null;

        // Password validation
        if ($newPassword) {
            if (strlen($newPassword) < 6) {
                throw new Exception("Password must be at least 6 characters long.");
            }
            if ($newPassword !== $confirmPassword) {
                throw new Exception("Passwords do not match.");
            }
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        // Check if the new email already exists in students, lecturers, or admins table
        $checkQuery = "SELECT email FROM student WHERE email = ? 
        UNION 
        SELECT email FROM lecturer WHERE email = ? 
        UNION 
        SELECT email FROM admin WHERE email = ?";
        
        $checkStmt = mysqli_prepare($connection, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "sss", $newEmail, $newEmail, $newEmail);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0 && $newEmail !== $email) {
            throw new Exception("Email already exists. Please use a different email.");
        }

        if ($role === "student") {
            $newName = mysqli_real_escape_string($connection, trim($_POST['name']));
            $newMatric = mysqli_real_escape_string($connection, trim($_POST['matric_no']));
            $newLevel = mysqli_real_escape_string($connection, $_POST['level']);
            $newCGPA = mysqli_real_escape_string($connection, $_POST['cgpa']);

            if (!is_numeric($newCGPA) || $newCGPA < 0.0 || $newCGPA > 5.0) {
                throw new Exception("Invalid CGPA. It must be between 0.0 and 5.0.");
            }

            $matricCheckQuery = "SELECT matricNumber FROM student WHERE matricNumber = ? AND email != ?";
            $matricCheckStmt = mysqli_prepare($connection, $matricCheckQuery);
            mysqli_stmt_bind_param($matricCheckStmt, "ss", $newMatric, $email);
            mysqli_stmt_execute($matricCheckStmt);
            $matricCheckResult = mysqli_stmt_get_result($matricCheckStmt);

            if (mysqli_num_rows($matricCheckResult) > 0) {
                throw new Exception("Matric number already exists. Please use a different one.");
            }

            $updateQuery = "UPDATE student SET name=?, matricNumber=?, level=?, cgpa=?, email=?";
            $params = [$newName, $newMatric, $newLevel, $newCGPA, $newEmail];
        } elseif ($role === "lecturer") {
            $newName = mysqli_real_escape_string($connection, trim($_POST['name']));
            $updateQuery = "UPDATE lecturer SET name=?, email=?";
            $params = [$newName, $newEmail];
        } elseif ($role === "admin") {
            $updateQuery = "UPDATE admin SET email=?";
            $params = [$newEmail];
        }

        if ($newPassword) {
            $updateQuery .= ", password=?";
            $params[] = $hashedPassword;
        }

        $updateQuery .= " WHERE email=?";
        $params[] = $email;

        $stmt = mysqli_prepare($connection, $updateQuery);
        mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['email'] = $newEmail;
            
            // Redirect based on role
            if ($role === "student") {
                header("Location: student.php");
            } elseif ($role === "lecturer") {
                header("Location: lecturer.php");
            } elseif ($role === "admin") {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            throw new Exception("Error updating information: " . mysqli_error($connection));
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}

?>

<div class="w-full flex justify-center items-center min-h-screen bg-gray-100">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-center">Edit Information</h2>

        <?php if (!empty($message)) : ?>
            <p class="text-center p-3 rounded mb-4 <?= strpos($message, 'Error') === false ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' ?>">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <form action="editinfo.php" method="POST">
            <?php if ($role === "student") : ?>
                <div class="mb-3">
                    <label class="block mb-1">Matric No</label>
                    <input type="text" name="matric_no" value="<?= htmlspecialchars($user['matricNumber']) ?>" class="w-full border rounded p-2" required>
                </div>
                <div class="mb-3">
                    <label class="block mb-1">Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="w-full border rounded p-2" required>
                </div>
                <div class="mb-3">
                    <label class="block mb-1">Level</label>
                    <select name="level" class="w-full border rounded p-2" required>
                        <option value="<?= $user['level'] ?>" selected><?= $user['level'] ?></option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                        <option value="300">300</option>
                        <option value="400">400</option>
                        <option value="500">500</option>
                        <option value="600">600</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block mb-1">CGPA</label>
                    <input type="text" name="cgpa" value="<?= htmlspecialchars($user['cgpa']) ?>" class="w-full border rounded p-2" required>
                </div>
            <?php elseif ($role === "lecturer") : ?>
                <div class="mb-3">
                    <label class="block mb-1">Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="w-full border rounded p-2" required>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="block mb-1">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-3">
                <label class="block mb-1">New Password (Min 6 characters)</label>
                <input type="password" name="password" class="w-full border rounded p-2">
            </div>
            <div class="mb-3">
                <label class="block mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" class="w-full border rounded p-2">
            </div>
            <button type="submit" class="w-full bg-green-600 text-white p-2 rounded">Update</button>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>
