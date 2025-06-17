@props(['currentRoute'])

@php
    $items = [
        ['img' => 'home.png', 'label' => 'Home', 'route' => 'applicant.dashboard'],
        ['img' => 'user.png', 'label' => 'Profile', 'route' => 'applicant.profile'],
        ['img' => 'application.png', 'label' => 'My Application', 'route' => 'applicant.application'],
        ['img' => 'folder.png', 'label' => '201 Files', 'route' => 'applicant.files'],
        ['img' => 'settings.png', 'label' => 'Settings', 'route' => 'applicant.settings'],
    ];
@endphp

<div x-data="{ open: window.innerWidth >= 640 }"
     x-init="$watch('open', value => {});"
     @resize.window="open = window.innerWidth >= 640">

    <!-- Sidebar (desktop only) -->
    <aside x-show="window.innerWidth >= 640"
           :class="open ? 'w-40' : 'w-16'"
           class="hidden sm:flex sticky top-0 bg-white shadow-md h-screen transition-all duration-300 flex-col">

        <!-- Toggle -->
        <div class="flex justify-end p-2">
            <button @click="open = !open" class="text-[#8B4513] focus:outline-none">
                <svg :class="open ? 'rotate-180' : ''"
                     class="w-5 h-5 transition-transform duration-300"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <!-- Vertical Menu -->
        <nav class="px-2 py-4 space-y-2">
            @foreach ($items as $item)
                <div class="relative group">
                    <a href="{{ route($item['route']) }}"
                       class="flex flex-col items-center p-2 rounded-md border transition duration-150 ease-in-out
                              {{ request()->routeIs($item['route']) 
                                  ? 'border-b-4 border-[#8B4513] text-[#8B4513] bg-[#F9F6F3] font-semibold'
                                  : 'text-[#8B4513] hover:bg-gray-100 border-gray-200' }}">
                        <img src="/images/{{ $item['img'] }}"
                             class="w-8 h-8 mb-1"
                             alt="{{ $item['label'] }}">
                        <template x-if="open">
                            <span class="text-xs leading-tight text-center">{{ $item['label'] }}</span>
                        </template>
                    </a>

                    <!-- Animated Tooltip -->
                    <div x-show="!open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-x-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                         x-transition:leave-end="opacity-0 -translate-x-2 scale-95"
                         class="absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 bg-[#8B4513] text-white text-xs rounded shadow-lg whitespace-nowrap z-50 hidden group-hover:block">
                        {{ $item['label'] }}
                    </div>
                </div>
            @endforeach
        </nav>
    </aside>

    <!-- Bottom Nav (mobile only) -->
    <nav class="sm:hidden fixed bottom-0 left-0 right-0 bg-white border-t shadow-inner z-50 flex justify-around py-2">
        @foreach ($items as $item)
            <a href="{{ route($item['route']) }}"
               class="flex flex-col items-center text-[#8B4513] text-xs {{ request()->routeIs($item['route']) ? 'font-semibold' : '' }}">
                <img src="/images/{{ $item['img'] }}" class="w-6 h-6 mb-0.5" alt="{{ $item['label'] }}">
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</div>
