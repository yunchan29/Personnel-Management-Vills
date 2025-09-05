<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
     <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/villslogo3.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f5f5f5] flex items-center justify-center min-h-screen font-sans">

    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-md border border-gray-200">
        <h1 class="text-2xl font-bold text-[#A06E45] mb-6 text-center uppercase tracking-wide">Forgot Password</h1>

        @if (session('status'))
            <div class="mb-4 text-green-700 bg-green-100 border border-green-200 p-3 rounded text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 text-red-700 bg-red-100 border border-red-200 p-3 rounded text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email" required autofocus
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#A06E45]" />
            </div>

            <button type="submit"
                class="w-full bg-[#A06E45] text-white font-semibold py-2 px-4 rounded-md hover:bg-[#8c5e3b] transition">
                Send Reset Link
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            <a href="{{ route('welcome') }}" class="text-[#A06E45] hover:underline">← Back to login</a>
        </div>
    </div>

</body>
</html>
