<?php include('header.php'); ?>

<div class="p-4">
  <h2 class="text-2xl font-bold mb-4">Lecturer Dashboard</h2>
  
  <div>
    <h3 class="text-xl font-semibold mb-2">Assigned Students</h3>
    <!-- Example table of assigned students -->
    <table class="min-w-full bg-white">
      <thead>
        <tr>
          <th class="py-2 px-4 border-b">Student Name</th>
          <th class="py-2 px-4 border-b">Student Rank</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="py-2 px-4 border-b">John Doe</td>
          <td class="py-2 px-4 border-b">1</td>
        </tr>
        <tr>
          <td class="py-2 px-4 border-b">Jane Smith</td>
          <td class="py-2 px-4 border-b">2</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<?php include('footer.php'); ?>
