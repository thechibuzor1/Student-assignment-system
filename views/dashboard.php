<?php
include('header.php');
include_once('../backend/config.php');
session_start();

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


// Check if the assignment button was clicked
if (isset($_POST['run_assignment'])) {
    runAssignmentAlgorithm();
    $assignmentMessage = "Assignment completed successfully!";
}

// Fetch students and lecturers
$students = mysqli_query($connection, "SELECT * FROM student ORDER BY cgpa DESC");
$lecturers = mysqli_query($connection, "SELECT * FROM lecturer ORDER BY rank DESC");

function runAssignmentAlgorithm() {
    global $connection;

    $students = mysqli_query($connection, "SELECT * FROM student ORDER BY cgpa DESC");
    $lecturers = mysqli_query($connection, "SELECT * FROM lecturer ORDER BY rank DESC");

    $lecturers_array = [];
    while ($lecturer = mysqli_fetch_assoc($lecturers)) {
        $lecturers_array[] = $lecturer;
    }

    $total_lecturers = count($lecturers_array);
    if ($total_lecturers === 0) {
        echo "<p class='text-red-600 text-center'>No lecturers available for assignment.</p>";
        return;
    }

    $index = 0;
    while ($student = mysqli_fetch_assoc($students)) {
        $lecturer = $lecturers_array[$index % $total_lecturers];
        $supervisorID = $lecturer['id'];
        $matricNumber = $student['matricNumber'];

        $updateQuery = "UPDATE student SET supervisorID = '$supervisorID' WHERE matricNumber = '$matricNumber'";
        mysqli_query($connection, $updateQuery);

        $index++;
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
</div>

</div>