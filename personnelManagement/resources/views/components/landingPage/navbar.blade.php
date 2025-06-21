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