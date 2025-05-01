<nav class="bg-[#BD9168] shadow-md px-6 py-2 flex justify-between items-center">
    <div class="flex items-center space-x-1">
        <a href="/"><img src="/images/villsLogoOnly.png" class="h-12 w-auto"></a>
        <a href="/"><img src="/images/villsName.png" class="h-12 w-auto"></a>
    </div>
    @auth
    <div class="relative">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center text-sm rounded-full focus:outline-none">
                    <img class="h-10 w-10 rounded-full object-cover border-2 border-white" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link href="/profile">Profile</x-dropdown-link>
                <form method="POST" action="/logout">
                    @csrf
                    <x-dropdown-link href="/logout" onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
    @endauth
</nav>
