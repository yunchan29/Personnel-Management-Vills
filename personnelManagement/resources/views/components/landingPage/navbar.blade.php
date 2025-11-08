 <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-transparent">
        <div class="w-full px-4 sm:px-6 lg:px-8 ">
            <div class="flex items-center justify-between h-16 md:h-20 items-center py-2">
                <div class="flex-shrink-0">
                    <a href="#">
                        <img src="{{ asset('/images/villsLogo2.png') }}" alt="Logo" class="h-12 md:h-16 w-auto">
                    </a>
                </div>

                <!-- Hamburger Button (Mobile Only) -->
                <div class="md:hidden">
                    <button id="menu-btn" class="text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#BD9168] p-2">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex space-x-4 lg:space-x-8 items-center">
                    <a href="#" class="nav-link text-sm lg:text-base">Home</a>
                    <a href="#jobs" class="nav-link text-sm lg:text-base">Jobs</a>
                    <a href="#footer" class="nav-link text-sm lg:text-base">About Us</a>
                    <button @click="activeModal = 'login'" class="nav-button text-sm lg:text-base">Login / Sign Up</button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden px-4 pt-4 pb-4 space-y-3 bg-transparent">
            <a href="#" class="block nav-link py-2 text-base">Home</a>
            <a href="#jobs" class="block nav-link py-2 text-base">Jobs</a>
            <a href="#footer" class="block nav-link py-2 text-base">About Us</a>
            <button @click="activeModal = 'login'" class="block nav-button text-center py-3 text-base w-full">Login / Sign Up</button>
        </div>
    </nav>
