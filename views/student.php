<?php  
    include('header.php');
    include('../backend/config.php');
     
    

// Check if student is logged in
if (!isset($_SESSION['matric_no'])) {
    header("Location: ../index.php"); // Redirect to login if not logged in
    exit();
}

// Get student data
$matric_no = $_SESSION['matric_no'];
$query = "SELECT supervisorID FROM student WHERE matricNumber = '$matric_no'";
$result = mysqli_query($connection, $query);
$row = mysqli_fetch_assoc($result);
$supervisorID = $row['supervisorID'] ?? null;
$lecturerName = null;

if ($supervisorID) {
    // Fetch lecturer name based on supervisorID
    $lecturerQuery = "SELECT name FROM lecturer WHERE id = '$supervisorID'";
    $lecturerResult = mysqli_query($connection, $lecturerQuery);
    if ($lecturerRow = mysqli_fetch_assoc($lecturerResult)) {
        $lecturerName = $lecturerRow['name'];
    } else {
        $supervisorID = null; // If lecturer doesn't exist, reset supervisorID
    }
}
?>


<div class="p-4">
  <h2 class="text-2xl font-bold mb-4">Student Dashboard</h2>

  <div>
    <h3 class="text-xl font-semibold mb-2">Your Assigned Supervisor</h3>
    <p class="bg-white p-4 rounded shadow">
      <?php if ($supervisorID && $lecturerName): ?>
        Supervisor: <strong><?php echo htmlspecialchars($lecturerName); ?></strong>
      <?php else: ?>
        <p>You will receive an email notification once you have been assigned a supervisor. Please check your inbox, including your spam folder.</p>
        <span class="text-red-600">You have not been assigned a supervisor yet.</span>
      <?php endif; ?>
    </p>
  </div>
</div>

<?php include('footer.php'); ?>
