
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - VillsPMS</title>
   <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/villslogo3.png') }}" type="image/png">
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

<div class="flex flex-col-reverse md:flex-row bg-white rounded-lg shadow-md w-full max-w-5xl min-h-[600px] max-h-[600px] fade-slide-up">

<div class="w-full md:w-1/2 bg-[#BD9168] flex flex-col justify-center items-center p-8 text-white rounded-b-lg md:rounded-l-lg">
  <div class="text-center max-w-md fade-slide-up-delay">
    <h1 class="text-2xl md:text-4xl font-alata mb-6 leading-relaxed">
      Welcome Back! Ready to land your next opportunity?
    </h1>
    <a href="{{ route('login') }}" 
       class="inline-block text-white bg-[#BD6F22] px-6 py-2 rounded-lg font-bold hover:bg-[#a35718] transition">
       Login
    </a>
  </div>
</div>


  <!-- Right Side (Register Form) -->
<div class="w-full md:w-1/2 bg-white flex flex-col p-8 rounded-t-lg md:rounded-l-lg overflow-hidden overflow-y-auto fade-slide-up">

      <a href="/"><img src="/images/villsLogo2.png" alt="Logo" class="w-auto h-16 mb-6"></a>
      <h1 class="text-2xl md:text-3xl font-bold text-center mb-2 text-[#BD6F22]">Create Account</h1>
      <div class="overflow-y-auto flex-1">
      <form method="POST" action="{{ route('register') }}" class="w-full max-w-sm" id="registerForm">
        @csrf

        <!-- First Name and Last Name -->
        <div class="flex space-x-2 mb-4">
          <div class="w-1/2">
            <label for="first_name" class="block text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
          </div>
          <div class="w-1/2">
            <label for="last_name" class="block text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
            <input type="text" name="last_name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
          </div>
        </div>

        <!-- Email -->
        <div class="mb-4">
          <label for="email" class="block text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
          <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
        </div>

        <!-- Birthdate and Gender -->
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

 <!-- Password Field -->
<div class="mb-4">
  <label for="password" class="block text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>

  <div class="flex items-start gap-4 relative">
    <!-- Password Input Wrapper -->
    <div class="relative w-full">
      <input type="password" name="password" id="password"
        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22] pr-20"
        required>
      <button type="button" onclick="togglePasswordVisibility('password', this)"
        class="absolute right-3 top-2 text-sm text-[#BD6F22]">Show</button>
    </div>

    
  </div>
</div>

<!-- Confirm Password (unchanged) -->
<div class="mb-4 relative">
  <label for="password_confirmation" class="block text-gray-700 mb-1">Re-type Password <span class="text-red-500">*</span></label>
  <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
  <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute right-3 top-9 text-sm text-[#BD6F22]">Show</button>
</div>

      

        <!-- Terms -->
        <div class="flex items-start mb-6">
          <input type="checkbox" name="terms" id="terms" required class="mt-1 mr-2 h-4 w-4 text-[#BD6F22]">
          <label for="terms" class="text-sm text-gray-700">
            By signing up, you agree to our
            <a href="#" class="text-[#BD6F22] hover:underline">Terms of Use</a> and
            <a href="#" class="text-[#BD6F22] hover:underline">Privacy Policy</a>.
            <span class="text-red-500">*</span>
          </label>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-center">
          <button type="submit" id="submitBtn" disabled class="bg-[#BD6F22] text-white px-8 py-2 rounded-lg w-40 opacity-50 cursor-not-allowed">
            Sign Up
          </button>
        </div>

      </form>
  </div>
    </div>
  </div>
</div>

<!-- Tooltip fixed to screen and dynamically placed -->
<div id="password-rules"
  class="fixed w-64 bg-white border rounded-lg shadow-lg p-4 text-sm text-gray-700 opacity-0 scale-95 transform transition-all duration-300 pointer-events-none z-[9999]">
  <ul class="space-y-1">
    <li id="rule-length" class="flex items-center gap-2">
      <span id="dot-length" class="w-2 h-2 rounded-full bg-red-500"></span>
      At least 8 characters
    </li>
    <li id="rule-uppercase" class="flex items-center gap-2">
      <span id="dot-uppercase" class="w-2 h-2 rounded-full bg-red-500"></span>
      At least one uppercase letter
    </li>
    <li id="rule-number" class="flex items-center gap-2">
      <span id="dot-number" class="w-2 h-2 rounded-full bg-red-500"></span>
      At least one number
    </li>
    <li id="rule-special" class="flex items-center gap-2">
      <span id="dot-special" class="w-2 h-2 rounded-full bg-red-500"></span>
      At least one special character
    </li>
    <li id="rule-match" class="flex items-center gap-2">
      <span id="dot-match" class="w-2 h-2 rounded-full bg-red-500"></span>
      Passwords must match
    </li>
  </ul>
</div>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // Restrict birthdate to users 18+
  flatpickr("#birth_date", {
    dateFormat: "m/d/Y",
    maxDate: new Date(new Date().setFullYear(new Date().getFullYear() - 18))
  });

  // Toggle password visibility
  function togglePasswordVisibility(id, btn) {
    const input = document.getElementById(id);
    const isPassword = input.type === "password";
    input.type = isPassword ? "text" : "password";
    btn.textContent = isPassword ? "Hide" : "Show";
  }

  // DOM Elements
  const passwordInput = document.getElementById("password");
  const confirmInput = document.getElementById("password_confirmation");
  const submitBtn = document.getElementById("submitBtn");

  const ruleContainer = document.getElementById("password-rules");
  const dotLength = document.getElementById("dot-length");
  const dotUppercase = document.getElementById("dot-uppercase");
  const dotNumber = document.getElementById("dot-number");
  const dotSpecial = document.getElementById("dot-special");
  const dotMatch = document.getElementById("dot-match");

  // Tooltip visibility toggle
  function showRuleContainer() {
    ruleContainer.classList.remove("opacity-0", "scale-95", "pointer-events-none");
    ruleContainer.classList.add("opacity-100", "scale-100");
  }

  function hideRuleContainer() {
    ruleContainer.classList.add("opacity-0", "scale-95", "pointer-events-none");
    ruleContainer.classList.remove("opacity-100", "scale-100");
  }

  // Update dot color based on validation
  function updateDot(dotElement, isValid) {
    dotElement.classList.remove("bg-red-500", "bg-green-500");
    dotElement.classList.add(isValid ? "bg-green-500" : "bg-red-500");
  }

  // Position tooltip to the left of password input
  function positionTooltip() {
    const rect = passwordInput.getBoundingClientRect();
    const tooltipWidth = 270;
    const gap = 12;

    if (window.innerWidth >= 768) {
      // Left of the input
      ruleContainer.style.position = "fixed";
      ruleContainer.style.top = `${rect.top + window.scrollY}px`;
      ruleContainer.style.left = `${rect.left + window.scrollX - tooltipWidth - gap}px`;
    } else {
      // Below input for small screens
      ruleContainer.style.position = "fixed";
      ruleContainer.style.top = `${rect.bottom + window.scrollY + gap}px`;
      ruleContainer.style.left = `${rect.left + window.scrollX}px`;
    }
  }

  // Validate password live
  function validatePasswordLive() {
    const password = passwordInput.value;
    const confirm = confirmInput.value;

    if (password.length > 0) {
      positionTooltip();
      showRuleContainer();
    } else {
      hideRuleContainer();
    }

    const isLength = password.length >= 8;
    const hasUpper = /[A-Z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    const matches = password && confirm && password === confirm;

    updateDot(dotLength, isLength);
    updateDot(dotUppercase, hasUpper);
    updateDot(dotNumber, hasNumber);
    updateDot(dotSpecial, hasSpecial);
    updateDot(dotMatch, matches);

    const allPassed = isLength && hasUpper && hasNumber && hasSpecial && matches;
    submitBtn.disabled = !allPassed;
    submitBtn.classList.toggle("opacity-50", !allPassed);
    submitBtn.classList.toggle("cursor-not-allowed", !allPassed);
  }

  passwordInput.addEventListener("input", validatePasswordLive);
  confirmInput.addEventListener("input", validatePasswordLive);
  window.addEventListener("resize", () => {
    if (!ruleContainer.classList.contains("opacity-0")) {
      positionTooltip();
    }
  });
</script>



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
