@props([
    'show' => 'showModal',
    'title' => '',
    'maxWidth' => 'max-w-md',
    'closeButton' => true
])

<div x-show="{{ $show }}" x-transition x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full {{ $maxWidth }} p-6 shadow-xl relative">
        @if($closeButton)
        <!-- Close button -->
        <button @click="{{ $show }} = false"
            class="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-xl font-bold">
            &times;
        </button>
        @endif

        @if($title)
        <!-- Title -->
        <h2 class="text-lg font-semibold text-[#BD6F22] mb-4">
            {{ $title }}
        </h2>
        @endif

        <!-- Dynamic Title Slot (for complex titles) -->
        @isset($dynamicTitle)
            <div class="text-lg font-semibold text-[#BD6F22] mb-4">
                {{ $dynamicTitle }}
            </div>
        @endisset

        <!-- Content -->
        <div class="modal-content">
            {{ $slot }}
        </div>

        <!-- Footer (optional) -->
        @isset($footer)
            <div class="flex justify-end gap-3 mt-6">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
