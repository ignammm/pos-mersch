@props([
    'label' => null,
    'name' => 'checkbox',
    'placeholder' => '',
    'value' => '',
    'error' => null,
    'required' => false,
    'disabled' => false,
    'helper' => null,
    'icon' => null,
])

@php
    //  Theme palette
    $theme = [
        'text' => 'text-slate-950',
        'bg' => 'bg-gray-100',
        'border' => 'border-gray-300',
        'focus' => 'focus:ring-2 focus:ring-blue-500',
        'checked' => 'checked:bg-blue-500 checked:border-blue-600',
        'error' => 'border-red-500 focus:ring-red-700 focus:border-red-500',
        'disabled' => 'bg-gray-50 cursor-not-allowed opacity-60',
        'helper' => 'text-gray-500',
        'label' => 'text-slate-950',
    ];

    $baseClass = implode(' ', [
        'w-4 h-4 rounded-sm transition-colors',
        'appearance-none cursor-pointer',
        'focus:outline-none',
        'border',
        $theme['bg'],
        $theme['border'],
        $theme['checked'],
        $theme['focus'],
        $error ? $theme['error'] : '',
        $disabled ? $theme['disabled'] : '',
    ]);
@endphp

<div class="w-full">
    <div class="flex flex-row items-center gap-x-2">
        {{-- Checkbox --}}
        <input name="{{ $name }}" id="{{ $name }}" type="checkbox" value="{{ old($name, $value) }}"
            @if ($required) required @endif @if ($disabled) disabled @endif
            {{ $attributes->merge(['class' => $baseClass]) }} />

        {{-- Label --}}
        @if ($label)
            <label for="{{ $name }}" class="block text-sm font-medium {{ $theme['label'] }}">
                {{ $label }}
                @if ($required)
                    <span class="text-destructive">*</span>
                @endif
            </label>
        @endif
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
