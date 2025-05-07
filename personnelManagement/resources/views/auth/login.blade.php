<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - VillsPMS</title>
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
        .fade-slide-up { animation: fadeSlideUp 0.8s ease-out both; }
        .fade-slide-up-delay { animation: fadeSlideUp 0.8s ease-out both; animation-delay: 0.4s; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="flex flex-col-reverse md:flex-row md:flex-nowrap bg-white rounded-lg shadow-md w-full max-w-5xl overflow-hidden min-h-[600px] space-y-6 md:space-y-0 fade-slide-up">

    <!-- Left Side (Signup Invite) -->
    <div class="w-full md:w-1/2 bg-[#BD9168] flex flex-col justify-center items-center p-8 text-white rounded-b-lg md:rounded-none fade-slide-up-delay">
        <div class="text-center max-w-md">
            <h1 class="text-2xl md:text-4xl font-alata mb-6 leading-relaxed">
                Create one now to start applying and land your next job online — it's quick and easy!
            </h1>
            <a href="{{ route('register') }}" class="inline-block text-white bg-[#BD6F22] px-6 py-2 rounded-lg font-bold hover:bg-[#a35718] transition">
                Sign Up
            </a>
        </div>
    </div>

    <!-- Right Side (Login) -->
    <div class="w-full md:w-1/2 bg-white p-8 flex flex-col justify-start items-center rounded-t-lg md:rounded-l-lg fade-slide-up">

        <!-- Logo -->
        <a href="/"><img src="/images/villsLogo2.png" alt="Logo" class="w-auto h-16 mb-6"></a>

        <!-- Login Header -->
        <h1 class="text-2xl md:text-3xl font-bold text-center mb-6 text-[#BD6F22]">Login</h1>

        <!-- Session Status Message -->
        @if (session('status'))
            <div class="mb-4 text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="mb-4 w-full max-w-sm text-sm text-red-600">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#BD6F22]">
            </div>

            <div class="flex items-center mb-6">
                <input type="checkbox" name="remember" id="remember" class="mr-2 h-4 w-4 text-[#BD6F22] bg-gray-100 border-gray-300 rounded focus:ring-[#BD6F22]">
                <label for="remember" class="text-gray-700 text-sm">Remember Me</label>
            </div>

            <div class="flex flex-col items-center space-y-3">
                <button type="submit" class="bg-[#BD6F22] text-white px-8 py-2 rounded-lg hover:bg-[#a35718] transition w-40">
                    Login
                </button>
                <a href="{{ route('password.request') }}" class="text-sm text-[#BD6F22] hover:underline">
                    Forgot Password?
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
