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
      <a href="/login" class="inline-block text-white bg-[#BD6F22] px-6 py-2 rounded-lg font-bold hover:bg-[#a35718] transition">
        Login
      </a>
    </div>
  </div>

  <!-- Right Side (Register Form) -->
  <div class="w-full md:w-1/2 bg-white flex flex-col p-8 rounded-t-lg md:rounded-l-lg overflow-y-auto fade-slide-up">
    <!-- Scrollable Container -->
    <div class="flex flex-col items-center w-full min-h-full">
      
      <!-- Logo -->
      <a href="/"><img src="/images/villsLogo2.png" alt="Logo" class="w-auto h-16 mb-6"></a>

      <!-- Register Text -->
      <h1 class="text-2xl md:text-3xl font-bold text-center mb-6 text-[#BD6F22]">Create Account</h1>

      <form method="POST" action="{{ route('register') }}" class="w-full max-w-sm">
        @csrf

        <!-- Row 1: First Name and Last Name -->
        <div class="flex space-x-2 mb-4">
          <div class="w-1/2">
            <label for="first_name" class="block text-gray-700 mb-1">First Name:</label>
            <input type="text" name="first_name" placeholder="First Name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
          </div>
          <div class="w-1/2">
            <label for="last_name" class="block text-gray-700 mb-1">Last Name:</label>
            <input type="text" name="last_name" placeholder="Last Name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
          </div>
        </div>

        <!-- Row 2: Email Address -->
        <div class="mb-4">
          <label for="email" class="block text-gray-700 mb-1">Email Address:</label>
          <input type="email" name="email" placeholder="Email Address" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
        </div>

        <!-- Row 3: Birthdate and Gender -->
        <div class="flex space-x-2 mb-4">
          <div class="w-1/2">
            <label for="birthdate" class="block text-gray-700 mb-1">Birthdate:</label>
            <input type="date" name="birthdate" id="birthdate" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
          </div>
          <div class="w-1/2">
            <label for="gender" class="block text-gray-700 mb-1">Gender:</label>
            <select name="gender" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
              <option value="" disabled selected>Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>
        </div>

        <!-- Row 4: Password -->
        <div class="mb-4">
          <label for="password" class="block text-gray-700 mb-1">Password:</label>
          <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
        </div>

        <!-- Row 5: Confirm Password -->
        <div class="mb-4">
          <label for="password_confirmation" class="block text-gray-700 mb-1">Re-type Password:</label>
          <input type="password" name="password_confirmation" placeholder="Re-type Password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
        </div>

        <!-- Checkbox Terms and Privacy -->
        <div class="flex items-start mb-6">
          <input type="checkbox" name="terms" id="terms" required class="mt-1 mr-2 h-4 w-4 text-[#BD6F22] bg-gray-100 border-gray-300 rounded focus:ring-[#BD6F22]">
          <label for="terms" class="text-sm text-gray-700">
            By signing up, you agree to our
            <a href="#" class="text-[#BD6F22] hover:underline">Terms of Use</a> and
            <a href="#" class="text-[#BD6F22] hover:underline">Privacy Policy</a>.
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

</body>
</html>
