<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'HR Admin Dashboard' }} - VillsPMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Alata&display=swap" rel="stylesheet">
    <style>
@keyframes checkmark {
    0% {
        stroke-dashoffset: 22;
    }
    100% {
        stroke-dashoffset: 0;
    }
}
@keyframes progressBar {
    0% {
        width: 100%;
    }
    100% {
        width: 0%;
    }
}
.animate-checkmark path {
    stroke-dasharray: 22;
    stroke-dashoffset: 22;
    animation: checkmark 0.5s ease-out forwards;
}
.animate-progress-bar {
    animation: progressBar 3s linear forwards;
}
</style>

    <style>
    .font-alata { font-family: 'Alata', sans-serif; }
    [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-alata min-h-screen">

    <x-hrAdmin.navbar />

    <div class="flex" x-data="{ open: true }">
    <x-hrAdmin.sidebar :currentRoute="Route::currentRouteName()" />

        <main class="flex-1 p-6">
            <div class="bg-white rounded-lg shadow-lg p-6 h-full">
            
                @yield('content')
            </div>
        </main>
    </div>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>
</html>
