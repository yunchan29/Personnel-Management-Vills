@props([
    'label' => '',
    'name' => '',
    'value' => '',
    'options' => [],
    'required' => false,
    'disabled' => false,
    'placeholder' => 'Select an option',
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

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except(['class', 'label', 'name', 'value', 'options', 'required', 'disabled', 'placeholder', 'error'])->merge(['class' => 'w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-brand-secondary focus:border-brand-secondary ' . ($disabled ? 'bg-gray-100 cursor-not-allowed' : '')]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @if($error || $errors->has($name))
        <p class="mt-1 text-sm text-red-600">{{ $error ?? $errors->first($name) }}</p>
    @endif
</div>
