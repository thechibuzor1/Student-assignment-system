<?php 
// Include the header file
include('views/header.php');

// Include database configuration
include_once('backend/config.php');

// Clear session data and regenerate session ID for security
session_unset();
session_regenerate_id(true);
?>

<!-- Centered login form container -->
<div class="w-full flex justify-center items-center h-screen">
  <div class="bg-white p-6 rounded shadow-md w-full max-w-sm">
    
    <!-- Login Heading -->
    <h2 class="text-xl font-bold mb-4">Login</h2>

    <!-- Login Form -->
    <form action="backend/login.php" method="POST">
      
      <!-- Email Input -->
      <div class="mb-4">
        <label class="block mb-1" for="email">Email</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          class="w-full border rounded p-2" 
          placeholder="Enter email" 
          required
        >
      </div>

      <!-- Password Input -->
      <div class="mb-4">
        <label class="block mb-1" for="password">Password</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="w-full border rounded p-2" 
          placeholder="Enter password" 
          required
        >
      </div>

      <!-- Role Selection Dropdown -->
      <div class="mb-4">
        <label class="block mb-1">Login as:</label>
        <select name="role" class="w-full border rounded p-2" required>
          <option value="student">Student</option>
          <option value="lecturer">Lecturer</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <!-- Login Button -->
      <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded">Login</button>
    </form>
    
    <!-- Register Link -->
    <p class="mt-4 text-center">
      Don't have an account? 
      <a href="views/register.php" class="text-blue-500">Register here</a>
    </p>
  </div>
</div>

<?php 
// Include the footer file
include('views/footer.php'); 
?>
