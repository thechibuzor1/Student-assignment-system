<?php include('header.php'); ?>

<div class="p-4">
  <h2 class="text-2xl font-bold mb-4">Admin Dashboard</h2>
  
  <!-- Ranking Management -->
  <div class="mb-6">
    <h3 class="text-xl font-semibold mb-2">Ranking Management</h3>
    
    <!-- Lecturer Ranking Form -->
    <form action="/backend/ranking.php" method="POST" class="mb-4">
      <h4 class="font-semibold">Lecturer Ranking</h4>
      <div class="mb-2">
        <input type="text" name="lecturer_name" placeholder="Lecturer Name" class="border p-2 rounded w-full" required>
      </div>
      <div class="mb-2">
        <input type="number" name="lecturer_rank" placeholder="Rank" class="border p-2 rounded w-full" required>
      </div>
      <button type="submit" class="bg-green-500 text-white p-2 rounded">Submit Lecturer Ranking</button>
    </form>
    
    <!-- Student Ranking Form -->
    <form action="/backend/ranking.php" method="POST">
      <h4 class="font-semibold">Student Ranking</h4>
      <div class="mb-2">
        <input type="text" name="student_name" placeholder="Student Name" class="border p-2 rounded w-full" required>
      </div>
      <div class="mb-2">
        <input type="number" name="student_rank" placeholder="Rank" class="border p-2 rounded w-full" required>
      </div>
      <button type="submit" class="bg-green-500 text-white p-2 rounded">Submit Student Ranking</button>
    </form>
  </div>
  
  <!-- Assignment Algorithm -->
  <div class="mb-6">
    <h3 class="text-xl font-semibold mb-2">Assignment</h3>
    <form action="/backend/assign.php" method="POST">
      <button type="submit" class="bg-blue-600 text-white p-2 rounded">Run Assignment Algorithm</button>
    </form>
  </div>
  
  <!-- Export Assignments -->
  <div>
    <h3 class="text-xl font-semibold mb-2">Export Assignments</h3>
    <form action="/backend/export.php" method="POST">
      <button type="submit" class="bg-purple-600 text-white p-2 rounded">Export as PDF/Excel</button>
    </form>
  </div>
</div>

<?php include('footer.php'); ?>
