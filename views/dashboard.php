<?php
include('header.php');
include_once('../backend/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../backend/libs/PHPMailer/Exception.php';
require '../backend/libs/PHPMailer/PHPMailer.php';
require '../backend/libs/PHPMailer/SMTP.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

    // Fetch students ranked by CGPA
    global $connection;
    // Fetch students and lecturers
    $students = mysqli_query($connection, "SELECT * FROM student ORDER BY cgpa DESC");
    $lecturers = mysqli_query($connection, "SELECT * FROM lecturer ORDER BY rank DESC");


    
// Function to send email notification to students
function sendEmailNotification($studentEmail, $studentName, $lecturerName, $lecturerEmail) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Change if using another provider
        $mail->SMTPAuth = true;
        $mail->Username = 'noreplycsc415grp3@gmail.com'; // Use your email
        $mail->Password = 'tsqo vzst jmpa zblp'; // Use your app password (not direct password)
        $mail->SMTPSecure =  PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465; // Use 465 for SSL

        // Email Details
        $mail->setFrom('no-reply@csc415grp3.wuaze.com', 'Student Assignment System');
        $mail->addAddress($studentEmail, $studentName);
        $mail->Subject = "Supervisor Assignment Notification";
        $mail->Body = "Dear $studentName,\n\nYou have been assigned a supervisor.\n\nSupervisor: $lecturerName\nEmail: $lecturerEmail\n\nBest regards,\nProject Student Assignment System";

        // Send Email
        $mail->send();
            
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}


// Check if the assignment button was clicked
if (isset($_POST['run_assignment'])) {
    runAssignmentAlgorithm();
    $assignmentMessage = "Assignment completed successfully!";
}

function runAssignmentAlgorithm() {
  
    global $connection;
    // Fetch students sorted by CGPA in descending order
    $students_query = mysqli_query($connection, "SELECT * FROM student ORDER BY cgpa DESC");
    $students = [];
    while ($student = mysqli_fetch_assoc($students_query)) {
        $students[] = $student;
    }

    // Fetch lecturers sorted by rank in descending order
    $lecturers_query = mysqli_query($connection, "SELECT * FROM lecturer ORDER BY rank DESC");
    $lecturers = [];
    while ($lecturer = mysqli_fetch_assoc($lecturers_query)) {
        $lecturers[] = $lecturer;
    }

    $total_lecturers = count($lecturers);
    if ($total_lecturers === 0) {
        echo "No lecturers available for assignment.";
        exit();
    }

    $index = 0; // Track lecturer index

    foreach ($students as $student) {
        $lecturer = $lecturers[$index % $total_lecturers]; // Assign based on round-robin with sorted ranking
        $supervisorID = $lecturer['id'];
        $matricNumber = $student['matricNumber'];

        // Assign student to lecturer
        $updateQuery = "UPDATE student SET supervisorID = '$supervisorID' WHERE matricNumber = '$matricNumber'";
        mysqli_query($connection, $updateQuery);

        $index++; // Move to the next lecturer in a cyclic manner
    }

    echo "Assignment completed successfully.";
}

// Check if the email button was clicked
if (isset($_POST['send_assignment_emails'])) {
    sendAssignmentEmailsToStudents();
    sendAssignmentEmailsToLecturers();
    $emailMessage = "Emails sent successfully!";
}


 
function sendAssignmentEmailsToStudents() {
    global $connection;

    // Fetch all assigned students and their supervisors
    $query = "SELECT s.name AS student_name, s.email AS student_email, 
                     l.name AS lecturer_name, l.email AS lecturer_email 
              FROM student s
              JOIN lecturer l ON s.supervisorID = l.id";

    $result = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        sendEmailNotification($row['student_email'], $row['student_name'], $row['lecturer_name'], $row['lecturer_email']);
    }

    echo "All assignment emails have been sent to students. <br>";
}

function sendAssignmentEmailsToLecturers() {
    global $connection;

    // Fetch all assigned students grouped by their lecturers
    $query = "SELECT l.name AS lecturer_name, l.email AS lecturer_email, 
                     s.name AS student_name, s.email AS student_email 
              FROM student s
              JOIN lecturer l ON s.supervisorID = l.id
              ORDER BY l.id";

    $result = mysqli_query($connection, $query);

    $lecturerAssignments = [];

    // Group students under their respective lecturers
    while ($row = mysqli_fetch_assoc($result)) {
        $lecturerEmail = $row['lecturer_email'];
        if (!isset($lecturerAssignments[$lecturerEmail])) {
            $lecturerAssignments[$lecturerEmail] = [
                'lecturer_name' => $row['lecturer_name'],
                'students' => []
            ];
        }
        $lecturerAssignments[$lecturerEmail]['students'][] = [
            'name' => $row['student_name'],
            'email' => $row['student_email']
        ];
    }

    // Send email to each lecturer with their assigned students
    foreach ($lecturerAssignments as $email => $details) {
        sendLecturerNotification($email, $details['lecturer_name'], $details['students']);
    }

    echo "All assignment emails have been sent to lecturers. <br>";
}

// Function to send an email notification to the lecturer
function sendLecturerNotification($lecturerEmail, $lecturerName, $students) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER; // Store in config.php
        $mail->Password = SMTP_PASS; // Store in config.php
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Email Setup
        $mail->setFrom('no-reply@csc415grp3.wuaze.com', 'Student Assignment System');
        $mail->addAddress($lecturerEmail, $lecturerName);
        $mail->Subject = "List of Assigned Students";

        // Construct the email body
        $message = "Dear $lecturerName,\n\nYou have been assigned the following students:\n\n";
        foreach ($students as $student) {
            $message .= "- {$student['name']} ({$student['email']})\n";
        }
        $message .= "\nPlease reach out to your assigned students accordingly.\n\nBest regards,\nUniversity Admin Team";

        $mail->Body = $message;

        // Send Email
        $mail->send();

    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}



?>
<div class="w-full p-6">

    <div class="flex mb-6 justify-between">
    <h2 class="text-3xl text-center md:text-left">Admin Dashboard</h2>
    <!-- Create Admin Button -->
 
    
    <form action="create_admin.php" method="POST">
        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded w-full md:w-auto">
            Add New Admin
        </button>
    </form>

</div>
<?php if (isset($_GET['success'])): ?>
    <p class="text-green-600"><?= htmlspecialchars($_GET['success']) ?></p>
<?php endif; ?>


<!-- Admin Management -->
<div class="mb-6 overflow-x-auto">
    <h3 class="text-xl font-semibold mb-4">Admin Management</h3>
    <table class="w-full border border-gray-300 rounded-lg text-sm md:text-base">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2">id</th>
                <th class="border p-2">Email</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $admins = mysqli_query($connection, "SELECT * FROM admin");
            while ($admin = mysqli_fetch_assoc($admins)) { ?>
                <tr class="text-center">
                    <td class="border p-2"> <?= htmlspecialchars($admin['id']) ?> </td>
                    <td class="border p-2"> <?= htmlspecialchars($admin['email']) ?> </td>
                    <td class="border p-2">
                        <form action="../backend/delete_admin.php" method="POST">
                            <input type="hidden" name="email" value="<?= $admin['email'] ?>">
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>



    <!-- Lecturer Management -->
    <div class="mb-6 overflow-x-auto">
        <h3 class="text-xl font-semibold mb-4">Lecturer Management</h3>
        <table class="w-full border border-gray-300 rounded-lg text-sm md:text-base">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Email</th>
                    <th class="border p-2">Rank</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($lecturer = mysqli_fetch_assoc($lecturers)) { ?>
                    <tr class="text-center">
                        <td class="border p-2"> <?= $lecturer['name'] ?> </td>
                        <td class="border p-2"> <?= $lecturer['email'] ?> </td>
                        <td class="border p-2">
                            <form action="../backend/update_rank.php" method="POST" class="flex flex-col md:flex-row items-center justify-center">
                                <input type="hidden" name="email" value="<?= $lecturer['email'] ?>">
                                <input type="number" name="rank" value="<?= $lecturer['rank'] ?>" class="border p-1 w-16 text-center">
                                <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded mt-2 md:mt-0 md:ml-2">Update</button>
                            </form>
                        </td>
                        <td class="border p-2">
                            <form action="../backend/delete_user.php" method="POST">
                                <input type="hidden" name="email" value="<?= $lecturer['email'] ?>">
                                <input type="hidden" name="role" value="lecturer">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Student Management -->
    <div class="mb-6 overflow-x-auto">
        <h3 class="text-xl font-semibold mb-4">Student Management</h3>
        <table class="w-full border border-gray-300 rounded-lg text-sm md:text-base">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">Matric Number</th>
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Email</th>
                    <th class="border p-2">CGPA</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = mysqli_fetch_assoc($students)) { ?>
                    <tr class="text-center">
                        <td class="border p-2"> <?= $student['matricNumber'] ?> </td>
                        <td class="border p-2"> <?= $student['name'] ?> </td>
                        <td class="border p-2"> <?= $student['email'] ?> </td>
                        <td class="border p-2"> <?= number_format($student['cgpa'], 2) ?> </td>
                        <td class="border p-2">
                            <form action="../backend/delete_user.php" method="POST">
                                <input type="hidden" name="email" value="<?= $student['email'] ?>">
                                <input type="hidden" name="role" value="student">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php  $query = "SELECT s.matricNumber, s.name AS student_name, s.email, s.cgpa, 
                 l.name AS lecturer_name, l.email AS lecturer_email 
          FROM student s
          LEFT JOIN lecturer l ON s.supervisorID = l.id
          ORDER BY s.cgpa DESC";

$assignments = mysqli_query($connection, $query);
?>

    <!-- Finalized Assignments -->
    <div class="mb-6 overflow-x-auto">
        <h3 class="text-xl font-semibold mb-4">Finalized Student-Supervisor Assignments</h3>
        <?php if (mysqli_num_rows($assignments) > 0): ?>
        <table class="w-full border border-gray-300 rounded-lg text-sm md:text-base">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">Matric Number</th>
                    <th class="border p-2">Student Name</th>
                    <th class="border p-2">Student Email</th>
                    <th class="border p-2">CGPA</th>
                    <th class="border p-2">Supervisor</th>
                    <th class="border p-2">Supervisor Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($assignments)): ?>
                    <tr class="text-center">
                        <td class="border p-2"><?= htmlspecialchars($row['matricNumber']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['student_name']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="border p-2"><?= number_format($row['cgpa'], 2) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['lecturer_name']) ?: 'Not Assigned' ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['lecturer_email']) ?: '-' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="text-red-600 text-center">No finalized assignments yet.</p>
        <?php endif; ?>
    </div>

    
    <!-- Run Assignment Algorithm -->
    <div class="mb-6 flex flex-col items-center md:items-start">
        <h3 class="text-xl font-semibold mb-4">Run Assignment Algorithm</h3>
        <form action="dashboard.php" method="POST">
            <input type="hidden" name="run_assignment" value="true">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full md:w-auto">
                Run Assignment
            </button>
        </form>
        <?php if (isset($assignmentMessage)) : ?>
            <p class="text-green-600 mt-2"><?= $assignmentMessage ?></p>
        <?php endif; ?>
    </div>


    <!-- Export Assignments -->
    <div class="flex flex-col md:flex-row md:space-x-4 items-center">
        <h3 class="text-xl font-semibold mb-4 md:mb-0">Export Assignments</h3>
        <form action="../backend/export_assignments.php" method="POST">
            <input type="hidden" name="format" value="pdf">
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded w-full md:w-auto">Export as PDF</button>
        </form>
        <form action="../backend/export_assignments.php" method="POST">
            <input type="hidden" name="format" value="excel">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded w-full md:w-auto mt-3 md:mt-0">Export as Excel</button>
        </form>
    </div>

    <!-- Send Assignment Emails Button -->
<div class="mb-6 flex flex-col items-center md:items-start mt-8">
    <h3 class="text-xl font-semibold mb-4">Send Assignment Emails</h3>
    <form action="dashboard.php" method="POST">
        <input type="hidden" name="send_assignment_emails" value="true">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded w-full md:w-auto">
            Send Emails
        </button>
    </form>
    <?php if (isset($emailMessage)) : ?>
        <p class="text-green-600 mt-2"><?= $emailMessage ?></p>
    <?php endif; ?>
</div>

<?php

?>

</div>

</div>