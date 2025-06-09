<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Employee Dashboard' }} - VillsPMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Alata&display=swap" rel="stylesheet">
    <style>
    .font-alata { font-family: 'Alata', sans-serif; }
    [x-cloak] { display: none !important; }
    </style>

    <!-- Litepicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css">
</head>
<body class="bg-gray-100 font-alata min-h-screen">

    <!-- Global Loading Screen -->
    <div id="loading-overlay" class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="flex items-center space-x-4">
            <svg class="animate-spin h-8 w-8 text-[#BD9168]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
            </svg>
            <span class="text-[#BD9168] font-semibold text-lg">Loading...</span>
        </div>
    </div>

    <x-employee.navbar />

    <div class="flex" x-data="{ open: true }">
        <x-employee.sidebar :currentRoute="Route::currentRouteName()" />

        <main class="flex-1 p-6">
            <div class="bg-white rounded-lg shadow-lg p-6 h-full">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Litepicker JS -->
    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>

    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data('leaveForm', () => ({
                open: false,
                init() {
                    new Litepicker({
                        element: this.$refs.dateRange,
                        singleMode: false,
                        format: 'MM/DD/YYYY',
                        numberOfMonths: 2,
                        numberOfColumns: 2
                    });
                }
            }));
        });

        // Make overlay logic conditional
        window.allowSubmit = true; // default to true for non-confirm forms

        const loadingOverlay = document.getElementById('loading-overlay');

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', () => {
                if (window.allowSubmit) {
                    loadingOverlay.classList.remove('hidden');
                }
            });
        });

        document.querySelectorAll('a[href]').forEach(link => {
            const href = link.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('javascript:') && !link.hasAttribute('target')) {
                link.addEventListener('click', () => {
                    setTimeout(() => {
                        loadingOverlay.classList.remove('hidden');
                    }, 50);
                });
            }
        });
    </script>
</body>
</html>
