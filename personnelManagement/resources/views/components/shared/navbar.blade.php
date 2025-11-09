@props(['showRoleText' => true])

<nav class="sticky top-0 z-50 bg-[#BD9168] shadow-md px-6 py-2 flex flex-col sm:flex-row sm:justify-between sm:items-center text-white">
    <!-- Logo & Date/Time Group -->
    <div class="flex flex-col sm:flex-row sm:items-center w-full sm:w-auto">
        <!-- Logo Row -->
        <div class="flex items-center justify-center gap-x-3">
            <a href="#" class="flex-shrink-0">
                <img src="/images/villsLogoOnly.png" alt="Logo Icon" class="h-12 sm:h-14 w-auto">
            </a>
            <a href="#" class="flex-shrink-0">
                <img src="/images/villsName.png" alt="Logo Text" class="h-10 sm:h-12 w-auto">
            </a>
        </div>

        <!-- Date/Time -->
        <div id="datetime" class="text-xs sm:text-sm font-medium text-white mt-1 sm:mt-0 sm:ml-6 text-center sm:text-left whitespace-nowrap"></div>
    </div>

    <!-- User Dropdown -->
    @auth
    @php
        $role = Auth::user()->role;
        $profileRoutes = [
            'applicant' => route('applicant.profile'),
            'employee' => route('employee.profile'),
            'hrAdmin' => route('hrAdmin.profile'),
            'hrStaff' => route('hrStaff.profile'),
        ];
        $roleTextMap = [
            'hrAdmin' => 'HR Admin',
            'hrStaff' => 'HR Staff',
            'employee' => 'Employee',
            'applicant' => 'Applicant'
        ];
        $roleText = $roleTextMap[$role] ?? '';
    @endphp
    <div class="mt-2 sm:mt-0 flex justify-center sm:justify-end">
        <x-shared.dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center text-sm rounded-full focus:outline-none gap-x-2">
                    <img class="h-9 w-9 rounded-full object-cover border-2 border-white"
                         src="{{ Auth::user()->profile_picture_url }}"
                         alt="{{ Auth::user()->name }}"
                         onerror="this.onerror=null; this.src='/images/defaultAvatar.png';">
                    @if($showRoleText && $roleText)
                        <span class="text-white font-medium">{{ $roleText }}</span>
                    @endif
                </button>
            </x-slot>

            <x-slot name="content">
                <x-shared.dropdown-link href="{{ $profileRoutes[$role] ?? '#' }}">
                    Profile
                </x-shared.dropdown-link>

                <form method="POST" action="/logout" class="m-0 p-0">
                    @csrf
                    <x-shared.dropdown-link href="/logout"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-shared.dropdown-link>
                </form>
            </x-slot>
        </x-shared.dropdown>
    </div>
    @endauth
</nav>

<!-- Date/Time Script -->
<script>
    function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'short', year: 'numeric', month: 'short',
            day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit',
            hour12: true
        };
        document.getElementById('datetime').textContent = now.toLocaleString('en-PH', options);
    }

    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>
