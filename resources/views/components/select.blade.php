@props([
    'label' => null,
    'name' => null,
    'options' => [], // ['value' => 'Label', ...]
    'placeholder' => null,
    'value' => '',
    'error' => null,
    'required' => false,
    'disabled' => false,
    'helper' => null,
    'icon' => null,
])

@php
    // 🎨 Theme palette for easy adjustments
    $theme = [
        'text' => 'text-slate-950',
        'bg' => 'bg-white',
        'border' => 'border-gray-300',
        'focus' => 'focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
        'error' => 'border-destructive focus:ring-destructive focus:border-destructive',
        'disabled' => 'bg-gray-50 cursor-not-allowed opacity-60',
        'helper' => 'text-gray-500',
        'label' => 'text-slate-950',
    ];

    // Build base input class with conditions
    $baseClass = implode(' ', [
        'appearance-none block w-full <px-4></px-4> py-3 rounded-lg shadow-sm transition-colors outline-none',
        $theme['text'],
        $theme['bg'],
        $error ? $theme['error'] : $theme['border'],
        $theme['focus'],
        $disabled ? $theme['disabled'] : '',
        $icon ? 'pl-10' : '',
    ]);
@endphp

<div class="w-full">
    {{-- Label --}}
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium {{ $theme['label'] }} mb-1">
            {{ $label }}
            @if ($required)
                <span class="text-destructive">*</span>
            @endif
        </label>
    @endif

    {{-- Container --}}
    <div class="relative">
        {{-- Optional icon --}}
        @if ($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                {!! $icon !!}
            </div>
        @endif

        {{-- Select --}}
        <select name="{{ $name }}" id="{{ $name }}" @if ($required) required @endif
            @if ($disabled) disabled @endif {{ $attributes->merge(['class' => $baseClass]) }}>
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach ($options as $key => $optionLabel)
                <option value="{{ $key }}"
                    {{ (string) old($name, $value) === (string) $key ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Helper and error messages --}}
    @if ($helper && !$error)
        <p class="mt-1 text-sm {{ $theme['helper'] }}">{{ $helper }}</p>
    @endif

    @if ($error)
        <p class="mt-1 text-sm text-destructive">{{ $error }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
    @enderror
</div>
