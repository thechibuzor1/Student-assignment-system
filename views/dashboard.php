<?php
include('header.php');
include_once('../backend/config.php');
session_start();

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch students
$students = mysqli_query($connection, "SELECT * FROM student");
// Fetch lecturers
$lecturers = mysqli_query($connection, "SELECT * FROM lecturer");
?>

<div class="w-full p-6">
    <h2 class="text-3xl font-bold mb-6">Admin Dashboard</h2>

    <!-- Lecturer Management -->
    <div class="mb-6">
        <h3 class="text-xl font-semibold mb-4">Lecturer Management</h3>
        <table class="w-full border border-gray-300 rounded-lg">
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
                    <tr>
                        <td class="border p-2"> <?= $lecturer['name'] ?> </td>
                        <td class="border p-2"> <?= $lecturer['email'] ?> </td>
                        <td class="border p-2">
                            <form action="../backend/update_rank.php" method="POST">
                                <input type="hidden" name="email" value="<?= $lecturer['email'] ?>">
                                <input type="number" name="rank" value="<?= $lecturer['rank'] ?>" class="border p-1 w-16">
                                <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded">Update</button>
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
    <div class="mb-6">
        <h3 class="text-xl font-semibold mb-4">Student Management</h3>
        <table class="w-full border border-gray-300 rounded-lg">
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
                    <tr>
                        <td class="border p-2"> <?= $student['matricNumber'] ?> </td>
                        <td class="border p-2"> <?= $student['name'] ?> </td>
                        <td class="border p-2"> <?= $student['email'] ?> </td>
                        <td class="border p-2"> <?= $student['cgpa'] ?> </td>
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

    <!-- Run Assignment Algorithm -->
    <div class="mb-6">
        <h3 class="text-xl font-semibold mb-4">Run Assignment Algorithm</h3>
        <form action="../backend/assign_students.php" method="POST">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Run Assignment</button>
        </form>
    </div>

    <!-- Export Assignments -->
    <div>
        <h3 class="text-xl font-semibold mb-4">Export Assignments</h3>
        <form action="../backend/export_assignments.php" method="POST">
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded">Export as PDF/Excel</button>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>
