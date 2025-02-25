<?php include('views/header.php'); ?>

<div class="w-full flex justify-center items-center h-64">
  <form action="login.php" method="POST" class="bg-white p-6 rounded shadow-md w-full max-w-sm">
    <h2 class="text-xl font-bold mb-4">Login</h2>
    <div class="mb-4">
      <label class="block mb-1" for="username">Username</label>
      <input type="text" id="username" name="username" class="w-full border rounded p-2" placeholder="Enter username" required>
    </div>
    <div class="mb-4">
      <label class="block mb-1" for="password">Password</label>
      <input type="password" id="password" name="password" class="w-full border rounded p-2" placeholder="Enter password" required>
    </div>
    <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded">Login</button>
  </form>
</div>

<?php include('views/footer.php'); ?>
