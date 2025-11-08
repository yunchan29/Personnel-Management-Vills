<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'VillsPMS') }} - VillsPMS</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/villslogo3.png') }}" type="image/png">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            primary: '#BD9168',
                            secondary: '#BD6F22',
                            tertiary: '#8B4513',
                            hover: '#a95e1d',
                            light: '#F9F6F3',
                            dark: '#6F3610'
                        }
                    },
                    fontFamily: {
                        alata: ['Alata', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Alata&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
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
        .font-alata {
            font-family: 'Alata', sans-serif;
        }
        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- Additional head content -->
    @stack('styles')
</head>
<body class="bg-gray-100 font-alata min-h-screen">

    <!-- Navbar -->
    @yield('navbar')

    <!-- Main Content with Sidebar -->
    @if(View::hasSection('sidebar'))
    <div class="flex overflow-hidden" x-data="{ open: true }">
        @yield('sidebar')

        <main class="flex-1 min-w-0 p-6">
            <div class="bg-white rounded-lg shadow-lg p-6 h-full">
                @yield('content')
            </div>
        </main>
    </div>
    @else
    <!-- Content without sidebar -->
    <main class="container mx-auto p-6">
        @yield('content')
    </main>
    @endif

    <!-- Loading Overlay -->
    @stack('modals')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')

</body>
</html>
