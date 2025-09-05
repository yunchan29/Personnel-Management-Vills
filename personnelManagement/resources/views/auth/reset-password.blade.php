<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
     <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/villslogo3.png') }}" type="image/png">
</head>
<body class="bg-[#EFE8E0] min-h-screen flex items-center justify-center font-sans">

    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-md border border-[#D4C4B0]">
        <h2 class="text-2xl font-bold text-[#BD6F22] mb-6 text-center tracking-wide">Reset Your Password</h2>

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-md text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label for="password" class="block font-semibold text-[#3A2C1D] mb-1">New Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="w-full border border-[#D4C4B0] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]"
                    required
                >
            </div>

            <div>
                <label for="password_confirmation" class="block font-semibold text-[#3A2C1D] mb-1">Confirm Password</label>
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="w-full border border-[#D4C4B0] rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#BD6F22]"
                    required
                >
            </div>

            <div>
                <button
                    type="submit"
                    class="w-full bg-[#BD6F22] hover:bg-[#a85d1f] text-white font-semibold py-2 px-4 rounded-md transition duration-200 shadow"
                >
                    Reset Password
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-[#BD6F22] hover:underline">Back to login</a>
        </div>
    </div>

</body>
</html>

<script src="https://cdn.tailwindcss.com"></script>