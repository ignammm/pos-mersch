@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'error' => null,
    'required' => false,
    'disabled' => false,
    'helper' => null,
    'icon' => null,
    'isPassword' => false,
    'iconPosition' => 'left',
    'displayError' => true,
])

@php

    $themeColors = [
        'bg' => 'bg-white',
        'bgHighlight' => 'bg-blue-100',
        'border' => 'border-gray-300',
        'borderHighlight' => 'border-blue-200',
        'borderRadius' => 'rounded-xl',
        'text' => 'text-gray-700',
        'placeholder' => 'text-gray-400',
        'icon' => 'text-gray-400',
        'error' => 'text-red-700',
        'errorBorder' => 'border-red-700',
        'errorFocus' => 'focus:ring-red-700',
        'errorText' => 'text-red-700',
        'disabledBg' => 'bg-gray-50',
        'disabledBorder' => 'border-gray-300',
        'helper' => 'text-gray-500',
        'focusRing' => 'focus:ring-blue-500',
        'focusBorder' => 'focus:border-blue-500',
    ];
@endphp

<div class="w-full">
    {{-- Label --}}
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium {{ $themeColors['text'] }} mb-2">
            {{ $label }}
            @if ($required)
                <span class="text-destructive">*</span>
            @endif
        </label>
    @endif

    {{-- Input Wrapper --}}
    <div x-data="{ showPassword: false }" class="relative">
        {{-- Icon --}}
        @if ($icon)
            <div
                class="absolute inset-y-0 {{ $iconPosition === 'left' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center pointer-events-none">
                <i class="{{ 'fas ' . $icon . ' ' . $themeColors['icon'] }}"></i>
            </div>
        @endif

        {{-- Input --}}
        <input
            @if ($isPassword) x-bind:type="showPassword ? 'text' : 'password'" @else type="{{ $type }}" @endif
            name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}" @if ($required) required @endif
            @if ($disabled) disabled @endif
            {{ $attributes->merge([
                'class' => collect([
                    'file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold',
                    'file:' . $themeColors['bg'],
                    'file:' . $themeColors['icon'],
                    'hover:file:' . $themeColors['bgHighlight'],
                    'appearance-none block w-full px-3 py-3 border shadow-sm transition-colors focus:ring-2',
                    $themeColors['borderRadius'],
                    $error || $errors->has($name) ? '' : $themeColors['focusBorder'],
                    $error || $errors->has($name) ? $themeColors['errorText'] : $themeColors['text'],
                    $themeColors['bg'],
                    $icon ? 'pl-10' : '',
                    $error || $errors->has($name)
                        ? "{$themeColors['errorBorder']} {$themeColors['errorFocus']}"
                        : "{$themeColors['border']} {$themeColors['focusRing']}",
                    $disabled ? "{$themeColors['disabledBg']} cursor-not-allowed opacity-60 {$themeColors['disabledBorder']}" : '',
                ])->filter()->implode(' '),
            ]) }} />

        {{-- Password Toggle --}}
        @if ($isPassword)
            <button type="button" x-on:click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 flex items-center px-3 {{ $themeColors['icon'] }}">
                <template x-if="!showPassword">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5
                            c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639
                            C20.577 16.49 16.64 19.5 12 19.5
                            c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0
                            3 3 0 0 1 6 0Z" />
                    </svg>
                </template>

                <template x-if="showPassword">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0
                            1.934 12C3.226 16.338 7.244 19.5
                            12 19.5c.993 0 1.953-.138
                            2.863-.395M6.228 6.228A10.451 10.451
                            0 0 1 12 4.5c4.756 0 8.773 3.162
                            10.065 7.498a10.522 10.522 0 0 1-4.293
                            5.774M6.228 6.228 3 3m3.228
                            3.228 3.65 3.65m7.894
                            7.894L21 21m-3.228-3.228-3.65-3.65m0
                            0a3 3 0 1 0-4.243-4.243m4.242
                            4.242L9.88 9.88" />
                    </svg>
                </template>
            </button>
        @endif
    </div>

    {{-- Helper / Error --}}
    @if ($helper && !$error)
        <p class="mt-1 ms-2 text-sm {{ $themeColors['helper'] }}">{{ $helper }}</p>
    @endif

    @if ($displayError)
        @if ($error)
            <span class="flex items-center gap-2">
                <i class="fas fa-circle text-[8px]"></i>
                <p class="mt-1 ms-2 text-sm {{ $themeColors['error'] }}">
                    {{ $error }}
                </p>
            </span>
        @endif

        @error($name)
            <span class="flex items-center gap-1 {{ $themeColors['error'] }}">
                <i class="fas fa-circle text-[8px]"></i>
                <p class="mt-1 ms-2 text-sm {{ $themeColors['error'] }}">{{ $message }}</p>
            </span>
        @enderror
    @endif
</div>
