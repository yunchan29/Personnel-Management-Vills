@props([
    'label' => '',
    'name' => '',
    'value' => '',
    'type' => 'text',
    'required' => false,
    'disabled' => false,
    'placeholder' => '',
    'pattern' => null,
    'maxlength' => null,
    'title' => null,
    'error' => null
])

<div {{ $attributes->only('class') }}>
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        @if($pattern) pattern="{{ $pattern }}" @endif
        @if($maxlength) maxlength="{{ $maxlength }}" @endif
        @if($title) title="{{ $title }}" @endif
        {{ $attributes->except(['class', 'label', 'name', 'value', 'type', 'required', 'disabled', 'placeholder', 'pattern', 'maxlength', 'title', 'error'])->merge(['class' => 'w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-brand-secondary focus:border-brand-secondary ' . ($disabled ? 'bg-gray-100 cursor-not-allowed' : '')]) }}
    >

    @if($error || $errors->has($name))
        <p class="mt-1 text-sm text-red-600">{{ $error ?? $errors->first($name) }}</p>
    @endif
</div>
