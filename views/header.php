<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Assignment System</title>
  <!-- Custom Styles (if needed) -->
  <link rel="stylesheet" href="/public/css/styles.css">
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col bg-gray-100">

<?php
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
}?>
  <!-- Header -->
  <header class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
      <h1 class="text-2xl font-bold">
        Project Assignment System
      </h1>
      
      <?php if (isset($_SESSION['email'])): ?>
      <!-- User Dropdown -->
      <div class="relative">
        <button id="userMenuButton" class="bg-gray-700 text-white px-4 py-2 rounded focus:outline-none">
          Account
        </button>
        <div id="userMenu" class="absolute right-0 mt-2 w-40 bg-white rounded shadow-lg hidden">
          <a href="editinfo.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Edit Info</a>
          <a href="../backend/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-200">Logout</a>
        </div>
      </div>
      
      <?php endif; ?>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-1 flex items-center justify-center container mx-auto mt-4">
    
  </main>

  <script>
    // Dropdown toggle script
    document.addEventListener("DOMContentLoaded", function () {
      const userMenuButton = document.getElementById("userMenuButton");
      const userMenu = document.getElementById("userMenu");

      userMenuButton?.addEventListener("click", function () {
        userMenu.classList.toggle("hidden");
      });

      // Close dropdown when clicking outside
      document.addEventListener("click", function (event) {
        if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
          userMenu.classList.add("hidden");
        }
      });
    });
  </script>

</body>
</html>
