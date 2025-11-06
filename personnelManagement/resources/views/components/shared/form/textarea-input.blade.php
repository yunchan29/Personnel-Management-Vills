@props([
    'label' => '',
    'name' => '',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'placeholder' => '',
    'rows' => 3,
    'maxlength' => null,
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

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        @if($maxlength) maxlength="{{ $maxlength }}" @endif
        {{ $attributes->except(['class', 'label', 'name', 'value', 'required', 'disabled', 'placeholder', 'rows', 'maxlength', 'error'])->merge(['class' => 'w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-brand-secondary focus:border-brand-secondary ' . ($disabled ? 'bg-gray-100 cursor-not-allowed' : '')]) }}
    >{{ old($name, $value) }}</textarea>

    @if($error || $errors->has($name))
        <p class="mt-1 text-sm text-red-600">{{ $error ?? $errors->first($name) }}</p>
    @endif
</div>
