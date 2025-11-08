{{-- Feedback Toast Component
     Usage: <x-shared.feedbackToast />
     Requires Alpine.js component to have: feedbackVisible and feedbackMessage properties
--}}
<div
    x-show="feedbackVisible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-4 rounded-xl shadow-lg z-50 w-80 overflow-hidden"
    x-cloak
>
    <div class="flex items-center gap-3">
        <svg class="w-6 h-6 text-white animate-checkmark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span class="font-semibold text-sm" x-text="feedbackMessage"></span>
    </div>
    <div class="mt-3 h-1 w-full bg-white/20 rounded overflow-hidden">
        <div class="h-full bg-white animate-progress-bar"></div>
    </div>
</div>
