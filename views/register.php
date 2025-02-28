<?php include('header.php'); 
      include_once('../backend/config.php') 
      
?>

<div class="w-full flex justify-center items-center h-screen">

  <div class="bg-white p-6 rounded shadow-md w-full max-w-md">
    <h2 class="text-xl font-bold mb-4 text-center">Register</h2>

    <div class="mb-4">
      <label class="block mb-1 text-center">Register as:</label>
      <select id="roleSelect" class="w-full border rounded p-2">
        <option value="student">Student</option>
        <option value="lecturer">Lecturer</option>
      </select>
    </div>

    <!-- Student Registration Form -->
<form id="studentForm" action="../backend/register_student.php" method="POST">
  <h3 class="text-lg font-bold mb-2">Student Registration</h3>

  <div class="mb-3">
    <label class="block mb-1">Matric No</label>
    <input type="text" name="matric_no" class="w-full border rounded p-2" placeholder="Enter Matric Number" required>
  </div>

  <div class="mb-3">
    <label class="block mb-1">Name</label>
    <input type="text" name="name" class="w-full border rounded p-2" placeholder="Enter Name" required>
  </div>

  <div class="mb-3">
    <label class="block mb-1">Level</label>
    <select name="level" class="w-full border rounded p-2" required>
      <option value="" disabled selected>Select Level</option>
      <option value="100">100</option>
      <option value="200">200</option>
      <option value="300">300</option>
      <option value="400">400</option>
      <option value="500">500</option>
      <option value="600">600</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="block mb-1">Email</label>
    <input type="email" name="email" class="w-full border rounded p-2" placeholder="Enter Email" required>
  </div>

  <div class="mb-3">
    <label class="block mb-1">CGPA</label>
    <input type="text" name="cgpa" class="w-full border rounded p-2" placeholder="Enter CGPA" required>
  </div>

  <div class="mb-3">
    <label class="block mb-1">Password</label>
    <input type="password" name="password" class="w-full border rounded p-2" placeholder="Enter a password" required>
  </div>

  <div class="mb-3">
    <label class="block mb-1">Confirm Password</label>
    <input type="password" name="cpassword" class="w-full border rounded p-2" placeholder="Confirm password" required>
  </div>

  <button type="submit" class="w-full bg-green-600 text-white p-2 rounded">Register as Student</button>
</form>


    <!-- Lecturer Registration Form -->
    <form id="lecturerForm" action="../backend/register_lecturer.php" method="POST" style="display: none;">
      <h3 class="text-lg font-bold mb-2">Lecturer Registration</h3>

      <div class="mb-3">
        <label class="block mb-1">Name</label>
        <input type="text" name="name" class="w-full border rounded p-2" placeholder="Enter Name" required>
      </div>

      <div class="mb-3">
        <label class="block mb-1">Email</label>
        <input type="email" name="email" class="w-full border rounded p-2" placeholder="Enter Email" required>
      </div>

      <div class="mb-3">
    <label class="block mb-1">Password</label>
    <input type="password" name="password" class="w-full border rounded p-2" placeholder="Enter a password" required>
  </div>

  <div class="mb-3">
    <label class="block mb-1">Confirm Password</label>
    <input type="password" name="cpassword" class="w-full border rounded p-2" placeholder="Confirm password" required>
  </div>


      <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded">Register as Lecturer</button>
    </form>

    <p class="mt-4 text-center">
      Already have an account? 
      <a href="../index.php" class="text-blue-500">Login here</a>
    </p>
  </div>

</div>

<script>
  const roleSelect = document.getElementById("roleSelect");
  const studentForm = document.getElementById("studentForm");
  const lecturerForm = document.getElementById("lecturerForm");

  roleSelect.addEventListener("change", function() {
    if (this.value === "student") {
      studentForm.style.display = "block";
      lecturerForm.style.display = "none";
    } else {
      studentForm.style.display = "none";
      lecturerForm.style.display = "block";
    }
  });
</script>

<?php include('footer.php'); ?>
