@props(['currentRoute'])

@php
    $items = [
        ['img' => 'home.png', 'label' => 'Home', 'route' => 'hrStaff.dashboard'],
        ['img' => 'employees.png', 'label' => 'Employees', 'route' => 'hrStaff.employees'],
        ['img' => 'briefcase.png', 'label' => 'Performance Evaluation', 'route' => 'hrStaff.perfEval'],
        ['img' => 'leaveForm.png', 'label' => 'Leave Forms', 'route' => 'hrStaff.leaveForm'],
        ['img' => 'archive.png', 'label' => 'Archive', 'route' => 'hrStaff.archive.index'],
        ['img' => 'settings.png', 'label' => 'Settings', 'route' => 'hrStaff.settings'],
    ];
@endphp

<div 
    x-data="{
        isDesktop: window.innerWidth >= 640,
        open: window.innerWidth >= 640,
    }"
    @resize.window="isDesktop = window.innerWidth >= 640; if(isDesktop){ open = true } else { open = false }"
>
    <!-- Sidebar (desktop only) -->
    <aside 
        x-show="isDesktop"
        :class="open ? 'w-40' : 'w-16'"
        class="hidden sm:flex sticky top-0 bg-white shadow-md h-screen transition-all duration-300 flex-col"
    >
        <!-- Toggle -->
        <div class="flex justify-end p-2">
            <button @click="open = !open" class="text-[#8B4513] focus:outline-none">
                <svg :class="open ? 'rotate-180' : ''" 
                     class="w-5 h-5 transition-transform duration-300"
                     fill="none" stroke="currentColor" stroke-width="2" 
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <!-- Vertical Menu -->
        <nav class="px-2 py-4 space-y-2">
            @foreach ($items as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex flex-col items-center p-2 rounded border transition duration-150
                          {{ request()->routeIs($item['route']) 
                              ? 'border-b-4 border-[#8B4513] text-[#8B4513] bg-[#F9F6F3] font-semibold'
                              : 'text-[#8B4513] hover:bg-gray-100 border-gray-200' }}">
                    <img src="/images/{{ $item['img'] }}" class="w-8 h-8 mb-1" alt="{{ $item['label'] }}">
                    <template x-if="open">
                        <span class="text-xs text-center">{{ $item['label'] }}</span>
                    </template>
                </a>
            @endforeach
        </nav>
    </aside>

    <!-- Bottom Nav (mobile only) -->
    <nav 
        x-show="!isDesktop"
        class="sm:hidden fixed bottom-0 left-0 right-0 bg-white border-t shadow-inner z-50 flex justify-around py-2"
    >
        @foreach ($items as $item)
            <a href="{{ route($item['route']) }}"
               class="flex flex-col items-center text-[#8B4513] text-xs {{ request()->routeIs($item['route']) ? 'font-semibold' : '' }}">
                <img src="/images/{{ $item['img'] }}" class="w-6 h-6 mb-0.5" alt="{{ $item['label'] }}">
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</div>
