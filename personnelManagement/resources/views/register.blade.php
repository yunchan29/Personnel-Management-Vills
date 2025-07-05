<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - VillsPMS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Alata&display=swap" rel="stylesheet">
  <style>
    .font-alata {
      font-family: 'Alata', sans-serif;
    }
    @keyframes fadeSlideUp {
      0% { opacity: 0; transform: translateY(30px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    .fade-slide-up {
      animation: fadeSlideUp 0.8s ease-out both;
    }
    .fade-slide-up-delay {
      animation: fadeSlideUp 0.8s ease-out both;
      animation-delay: 0.4s;
    }
  </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="flex flex-col-reverse md:flex-row bg-white rounded-lg shadow-md w-full max-w-5xl overflow-hidden min-h-[600px] max-h-[600px] fade-slide-up">

  <!-- Left Side (Login Invite) -->
  <div class="w-full md:w-1/2 bg-[#BD9168] flex flex-col justify-center items-center p-8 text-white rounded-b-lg md:rounded-none">
    <div class="text-center max-w-md fade-slide-up-delay">
      <h1 class="text-2xl md:text-4xl font-alata mb-6 leading-relaxed">
        Welcome Back! Ready to land your next opportunity?
      </h1>
      <a href="{{ route('login') }}" class="inline-block text-white bg-[#BD6F22] px-6 py-2 rounded-lg font-bold hover:bg-[#a35718] transition">
        Login
      </a>
    </div>
  </div>

  <!-- Right Side (Register Form) -->
  <div class="w-full md:w-1/2 bg-white flex flex-col p-8 rounded-t-lg md:rounded-l-lg overflow-y-auto fade-slide-up">
    <div class="flex flex-col items-center w-full min-h-full">
      
      <!-- Logo -->
      <a href="/"><img src="/images/villsLogo2.png" alt="Logo" class="w-auto h-16 mb-6"></a>

      <!-- Register Text -->
      <h1 class="text-2xl md:text-3xl font-bold text-center mb-2 text-[#BD6F22]">Create Account</h1>
      

   <form method="POST" action="{{ route('register') }}" class="w-full max-w-sm" id="registerForm">

        @csrf

        <!-- Row 1: First Name and Last Name -->
        <div class="flex space-x-2 mb-4">
          <div class="w-1/2">
            <label for="first_name" class="block text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" placeholder="First Name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
          </div>
          <div class="w-1/2">
            <label for="last_name" class="block text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
            <input type="text" name="last_name" placeholder="Last Name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
          </div>
        </div>

        <!-- Row 2: Email Address -->
        <div class="mb-4">
          <label for="email" class="block text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
          <input type="email" name="email" placeholder="Email Address" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
        </div>

        <!-- Row 3: Birthdate and Gender -->
        <div class="flex space-x-2 mb-4">
          <div class="w-1/2">
            <label for="birth_date" class="block text-gray-700 mb-1">Birthdate <span class="text-red-500">*</span></label>
            <input type="text" name="birth_date" id="birth_date" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
          </div>
          <div class="w-1/2">
            <label for="gender" class="block text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
            <select name="gender" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
              <option value="" disabled selected>Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>
        </div>

        <!-- Row 4: Password -->
        <div class="mb-4 relative">
          <label for="password" class="block text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
          <input type="password" name="password" id="password" placeholder="Password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
          <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute right-3 top-9 text-sm text-[#BD6F22]">Show</button>
        </div>

        <!-- Row 5: Confirm Password -->
        <div class="mb-4 relative">
          <label for="password_confirmation" class="block text-gray-700 mb-1">Re-type Password <span class="text-red-500">*</span></label>
          <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Re-type Password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
          <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute right-3 top-9 text-sm text-[#BD6F22]">Show</button>
        </div>

        <!-- Checkbox Terms and Privacy -->
        <div class="flex items-start mb-6">
          <input type="checkbox" name="terms" id="terms" required class="mt-1 mr-2 h-4 w-4 text-[#BD6F22] bg-gray-100 border-gray-300 rounded focus:ring-[#BD6F22]">
          <label for="terms" class="text-sm text-gray-700">
            By signing up, you agree to our
            <a href="#" class="text-[#BD6F22] hover:underline">Terms of Use</a> and
            <a href="#" class="text-[#BD6F22] hover:underline">Privacy Policy</a>.
            <span class="text-red-500">*</span>
          </label>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-center">
          <button type="submit" class="bg-[#BD6F22] text-white px-8 py-2 rounded-lg hover:bg-[#a35718] transition w-40">
            Sign Up
          </button>
        </div>

      </form>
    </div>
  </div>

</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Script Section -->
<script>
  // Restrict birthdate input to users 18+
  document.addEventListener("DOMContentLoaded", function () {
    const birthdateInput = document.getElementById("birth_date");
    const today = new Date();
    const year = today.getFullYear() - 18;
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const maxDate = `${year}-${month}-${day}`;
    birthdateInput.max = maxDate;
  });

  // Toggle password visibility with Show/Hide text
  function togglePasswordVisibility(id, btn) {
    const input = document.getElementById(id);
    if (input.type === "password") {
      input.type = "text";
      btn.textContent = "Hide";
    } else {
      input.type = "password";
      btn.textContent = "Show";
    }
  }
</script>

<script>
  flatpickr("#birth_date", {
  dateFormat: "m/d/Y", // MM/DD/YYYY
  maxDate: new Date(new Date().setFullYear(new Date().getFullYear() - 18)) // 18+ limit
});
</script>

<script>
  document.getElementById("registerForm").addEventListener("submit", function (e) {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("password_confirmation").value;

    const errors = [];

    if (password.length < 8) {
      errors.push("Password must be at least 8 characters long.");
    }
    if (!/[A-Z]/.test(password)) {
      errors.push("Password must contain at least one uppercase letter.");
    }
    if (!/[0-9]/.test(password)) {
      errors.push("Password must contain at least one number.");
    }
    if (!/[!@#$%^&*(),.?\":{}|<>]/.test(password)) {
      errors.push("Password must contain at least one special character.");
    }
    if (password !== confirmPassword) {
      errors.push("Passwords do not match.");
    }

    if (errors.length > 0) {
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'Please check your password',
        html: errors.join('<br>'),
        confirmButtonColor: '#BD6F22'
      });
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session("success") }}',
        confirmButtonColor: '#BD6F22',
        timer: 2500,
        showConfirmButton: false
      });
    });
  </script>
@endif

</body>
</html>
