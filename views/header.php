<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Assignment System</title>
  <!-- Custom Styles (if needed) -->
  <link rel="stylesheet" href="/public/css/styles.css">
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col bg-gray-100">

  <!-- Header -->
  <header class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
      <h1 class="text-2xl font-bold">
        Project Assignment System
      </h1>
      <div class="flex space-x-4">
        <a href="editinfo.php" class="bg-yellow-400 text-black px-4 py-2 rounded">Edit Info</a>
        <a href="../backend/logout.php" class="bg-red-500 text-white px-4 py-2 rounded">Logout</a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-1 flex items-center justify-center container mx-auto mt-4">
    
  </main>

  <script>
    // Function to clear all forms on submit
    document.addEventListener("DOMContentLoaded", function() {
      document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", function() {
          setTimeout(() => form.reset(), 100); // Clear after a short delay
        });
      });
    });
  </script>

</body>
</html>
