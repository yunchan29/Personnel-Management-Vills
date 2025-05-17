<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VillsPMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .nav-link {
            color: #374151;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #BD9168;
        }
        .nav-button {
            background-color: #BD9168;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .nav-button:hover {
            background-color: #a7744f;
        }
        .hero {
            background-image: url('/images/headerImage.png');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            animation: fadeIn 2s ease-in-out;
        }
        .hero h1 {
            font-size: 3rem;
            color: white;
            font-weight: bold;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            animation: slideDown 1.5s ease-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        @keyframes slideDown {
            0% { transform: translateY(-50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">

    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-18 items-center">
              
                <div class="flex-shrink-0">
                    <a href="#">
                        <img src="{{ asset('/images/villsLogo2.png') }}" alt="Logo" class="h-16 w-auto">
                    </a>
                </div>

                <!-- Hamburger Button (Mobile Only) -->
                <div class="md:hidden">
                    <button id="menu-btn" class="text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#BD9168]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#" class="nav-link">Home</a>
                    <a href="#" class="nav-link">About Us</a>
                    <a href="#" class="nav-link">Contact Us</a>
                    <a href="/register" class="nav-button">Login / Sign Up</a>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden px-4 pt-4 pb-2 space-y-2">
            <a href="#" class="block nav-link">Home</a>
            <a href="#" class="block nav-link">About Us</a>
            <a href="#" class="block nav-link">Contact Us</a>
            <a href="#" class="block nav-button text-center">Login / Sign Up</a>
        </div>
    </nav>

    <!-- Header / Hero Section -->
    <header class="hero">
        <h1></h1>
    </header>

    <!-- Job Listing Section -->
    <section class="w-full">
        <!-- Header -->
        <div class="bg-[#BD9168] w-full h-16 flex items-center justify-center animate-fadeIn">
            <h2 class="text-white text-3xl font-bold">Job Listing</h2>
        </div>

        <!-- Content Canvas -->
        <div class="p-6 bg-white animate-fadeIn delay-200">
            <!-- Search Bar -->
            <div class="flex items-center gap-4 mb-6">
               <form method="GET" action="{{ route('welcome') }}" class="flex items-center gap-4 mb-6">
    <label for="search" class="text-lg font-medium text-gray-700">Search Position</label>
    <input 
        type="text" 
        name="search"
        id="search" 
        placeholder="Enter job title..." 
        class="border border-gray-300 rounded-lg px-4 py-2 w-full max-w-md focus:outline-none focus:ring-2 focus:ring-[#BD9168]"
        value="{{ request('search') }}"
    />
    <button type="submit" class="bg-[#BD9168] text-white px-4 py-2 rounded-lg hover:bg-[#a37653] flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-3.35z" />
        </svg>
        Search
    </button>
</form>

            </div>

            <!-- Job cards will go here next -->
            <div class="p-6 bg-white">
                <!-- Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    @forelse($jobs as $job)
        <div class="flex items-start p-6 border rounded-lg shadow-sm bg-white gap-6 transform hover:scale-105 transition duration-300">
            <!-- Left Side -->
            <div class="flex flex-col items-start gap-4">
                <div class="text-gray-500 text-sm">
                    <p>Last Posted: <span class="font-medium">{{ $job->created_at->diffForHumans() }}</span></p>
                    <p>Apply until: <span class="font-medium">{{ \Carbon\Carbon::parse($job->apply_until)->format('F d, Y') }}</span></p>
                </div>
                <a href="{{ route('job.show', $job->id) }}" class="bg-[#BD9168] text-white px-6 py-2 rounded-md hover:bg-[#a37653] flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                    Apply Now
                </a>
            </div>

            <!-- Right Side -->
            <div class="flex flex-col gap-2">
                <h3 class="text-[#BD9168] text-2xl font-bold">{{ $job->job_title }}</h3>
                <p class="text-black font-semibold">{{ $job->company_name }}</p>

                <div class="flex items-start gap-2 mt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#BD9168] mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 01-8 0M12 14a8 8 0 00-8 8h16a8 8 0 00-8-8z" />
                    </svg>
                    <div>
                        <p class="font-semibold">Qualifications:</p>
                     @if (!empty($job->qualifications) && is_array($job->qualifications))
    <ul class="list-disc list-inside text-black">
        @foreach ($job->qualifications as $qual)
            <li>{{ $qual }}</li>
        @endforeach
    </ul>
@else
    <p>No qualifications listed.</p>
@endif


                    </div>
                </div>

                <div class="flex items-center gap-2 mt-4 text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#BD9168]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414m0 0L9.172 8.172m4.242 4.242A4 4 0 1116.657 7.343a4 4 0 01-4.243 4.243z" />
                    </svg>
                    <p>{{ $job->location }}</p>
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-500">No job listings available right now.</p>
    @endforelse
</div>


                          <!-- Pagination -->
<div class="mt-6">
   {{ $jobs->links('vendor.pagination.tailwind') }}

</div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-[#BD9168] text-white py-10 flex flex-col items-center mt-10 animate-fadeIn">
        <div class="flex items-center gap-3 mb-6">
            <img src="/images/villslogo3.png" alt="Vills Manpower Logo" class="w-20 h-20">
            <img src="/images/villsName.png" alt="Vills Manpower Recruitment Agency" class="h-20 w-auto">
        </div>

        <p class="text-center max-w-xl text-sm mb-6">
            Vills Manpower Recruitment Agency is a...........
        </p>

        <div class="border-t border-white w-3/4 mb-6"></div>

        <div class="flex flex-col gap-4 text-sm">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22,12A10,10..."></path>
                </svg>
                <span>Follow us on Facebook</span>
            </div>
        </div>
    </footer>

    <!-- Script for mobile menu -->
    <script>
        document.getElementById('menu-btn').addEventListener('click', function () {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="flex items-center space-x-4">
        <svg class="animate-spin h-8 w-8 text-[#BD9168]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        <span class="text-[#BD9168] font-semibold text-lg">Loading...</span>
    </div>
</div>
<script>
    const loadingOverlay = document.getElementById('loading-overlay');

    // Show loader on form submit
    const searchForm = document.querySelector('form[action="{{ route('welcome') }}"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function () {
            loadingOverlay.classList.remove('hidden');
        });
    }

    // Show loader on pagination clicks
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.pagination a').forEach(function (el) {
            el.addEventListener('click', function () {
                loadingOverlay.classList.remove('hidden');
            });
        });
    });

    // Optional: show loader on Apply Now button clicks
    document.querySelectorAll('a[href^="/job/"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            loadingOverlay.classList.remove('hidden');
        });
    });
</script>



</body>
</html>
