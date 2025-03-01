<?php 
include('header.php'); 
include('../backend/config.php'); 

// Check if lecturer is logged in
if (!isset($_SESSION['lecturer_id'])) {
    header("Location: ../index.php"); // Redirect to login if not logged in
    exit();
}

$lecturer_id = $_SESSION['lecturer_id'];

// Fetch students assigned to this lecturer
$query = "SELECT name, matricNumber, cgpa FROM student WHERE supervisorID = ? ORDER BY cgpa DESC";

if ($stmt = mysqli_prepare($connection, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $lecturer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    die("Database query error!");
}
?>


<div class="w-full p-4">
  <h2 class="text-2xl font-bold mb-4">Lecturer Dashboard</h2>
  
  <div>
    <h3 class="text-xl font-semibold mb-2">Assigned Students</h3>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <table class="w-full max-w-md bg-white border border-gray-300">
        <thead>
          <tr class="bg-gray-200">
            <th class="py-2 px-4 border-b">Student Name</th>
            <th class="py-2 px-4 border-b">Matric Number</th>
             
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($row['name']); ?></td>
              <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($row['matricNumber']); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="text-red-600">No students assigned yet.</p>
    <?php endif; ?>
  </div>
</div>

<?php include('footer.php'); ?>
