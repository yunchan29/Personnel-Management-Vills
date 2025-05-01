<aside x-data="{ open: true }" :class="open ? 'w-40' : 'w-16'" class="bg-white shadow-md h-screen transition-all duration-300">
    <div class="flex justify-end p-2">
        <button @click="open = !open" class="text-[#8B4513] focus:outline-none">
            <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
    </div>
    <nav class="px-2 py-4 space-y-2">
        @foreach ([
            ['img' => 'home.png', 'label' => 'Home'],
            ['img' => 'user.png', 'label' => 'Profile'],
            ['img' => 'application.png', 'label' => 'My Application'],
            ['img' => 'folder.png', 'label' => '201 Files'],
            ['img' => 'settings.png', 'label' => 'Settings']
        ] as $item)
        <a href="#" class="flex flex-col items-center text-[#8B4513] hover:bg-gray-100 p-2 rounded border border-gray-200">
            <img src="/images/{{ $item['img'] }}" class="w-8 h-8 mb-1" alt="{{ $item['label'] }}">
            <template x-if="open"><span class="text-xs">{{ $item['label'] }}</span></template>
        </a>
        @endforeach
    </nav>
</aside>
