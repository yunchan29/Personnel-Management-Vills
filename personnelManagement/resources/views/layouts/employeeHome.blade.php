<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Employee Dashboard' }} - VillsPMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Alata&display=swap" rel="stylesheet">
    <style>.font-alata { font-family: 'Alata', sans-serif; }</style>
    <!-- Litepicker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css">

</head>
<body class="bg-gray-100 font-alata min-h-screen">

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
</script>
</body>
</html>
