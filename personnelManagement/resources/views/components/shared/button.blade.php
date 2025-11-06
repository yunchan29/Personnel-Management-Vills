@props([
    'variant' => 'primary',
    'loading' => false,
    'loadingText' => 'Processing...',
    'type' => 'button'
])

@php
    $variantClasses = [
        'primary' => 'bg-[#BD6F22] text-white hover:bg-[#a95e1d]',
        'secondary' => 'bg-[#8B4513] text-white hover:bg-[#6F3610]',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
        'outline' => 'bg-white text-[#BD6F22] border-2 border-[#BD6F22] hover:bg-[#F9F6F3]',
    ];
    $classes = $variantClasses[$variant] ?? $variantClasses['primary'];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "px-4 py-2 text-sm rounded disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transition duration-150 $classes"]) }}
    :disabled="{{ $loading }}"
>
    <template x-if="{{ $loading }}">
        <x-shared.loading-spinner />
    </template>
    <span x-text="{{ $loading }} ? '{{ $loadingText }}' : '{{ $slot }}'">
        {{ $slot }}
    </span>
</button>
