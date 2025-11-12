@props([
    'required' => false,
    'disabled' => false,
    'error' => null,
    'label' => null,
])

@php
    $theme = [
        'text' => 'text-slate-950',
        'bg' => 'bg-white',
        'border' => 'border-gray-300',
        'focus' => 'focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
        'error' => 'border-red-500 focus:ring-red-500 focus:border-red-700',
        'disabled' => 'bg-gray-50 cursor-not-allowed opacity-60',
        'helper' => 'text-gray-500',
        'label' => 'text-slate-950',
        'icon' => 'text-gray-400',
        'errorText' => 'text-red-700',
        'itemfocus' => 'focus:ring-blue-500 focus:border-blue-500',
        'itemHover' => 'hover:bg-blue-400/50',
    ];

    $baseClass = implode(' ', [
        'appearance-none block w-full px-3 py-3 border shadow-sm transition-colors focus:ring-2 rounded-lg',
        $error ? $theme['errorText'] : $theme['text'],
        $theme['bg'],
        $error ? $theme['error'] : $theme['border'],
        $theme['focus'],
        $disabled ? $theme['disabled'] : '',
        $icon ? 'pl-10' : '',
    ]);

    $itemClass = implode(' ', [
        'appearance-none flex flex-1 flex-row items-center gap-2 text-left px-4 py-2 focus:outline-none focus:ring-2',
        $theme['itemfocus'],
        $theme['itemHover'],
    ]);

@endphp

<div class="w-full">
    {{-- Label --}}
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium {{ $theme['text'] }} mb-2">
            {{ $label }}
            @if ($required)
                <span class="text-destructive">*</span>
            @endif
        </label>
    @endif
    <div class="relative" x-data="{ open: false }" @click.away="open = false">
        {{-- Input Wrapper --}}
        <div class="relative flex items-center" x-ref="input">
            @if ($icon)
                <div
                    class="absolute inset-y-0 {{ $iconPosition === 'left' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center pointer-events-none">
                    <i class="fas {{ $icon }} {{ $error ? $theme['errorText'] : $theme['icon'] }}"></i>
                </div>
            @endif

            {{-- Input --}}
            <input type="text" wire:model.live.debounce.300ms="value" name="{{ $name }}"
                placeholder="{{ $placeholder }}" @disabled($disabled) @click="open = true"
                @keydown="open = true" @keydown.up="$focus.within($refs.suggestions).last()"
                @keydown.down="$focus.within($refs.suggestions).first()"
                {{ $attributes->merge(['class' => $baseClass]) }} />

            {{-- Loading Spinner --}}
            <div wire:loading wire:target="value" class="absolute right-3">
                <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
            </div>
        </div>

        {{-- Suggestions List --}}
        <div x-show="open" x-transition.origin.top x-ref="suggestions"
            class="absolute w-full border-gray-300 rounded-md rounded-t-none shadow-lg bg-gray-100 focus:outline-none z-50"
            @keydown.up.prevent="$focus.wrap().previous()" @keydown.down.prevent="$focus.wrap().next()"
            @keydown.left.prevent="$focus.within($refs.input).first()"
            @keydown.right.prevent="$focus.within($refs.input).first()"
            @keydown.escape.prevent="open = false; $focus.within($refs.input).first()"
            @keydown.enter.prevent="open = false;">
            @if ($results->isNotEmpty())
                {{-- <ul
                    class="absolute left-0 right-0 rounded-lg rounded-t-none border z-10 overflow-hidden
                    bg-white border-gray-200 text-gray-900">
                    @foreach ($results as $item)
                        <li wire:click="selectSuggestion({{ $item->id }})" x-on:click="open = false"
                            class="px-4 py-2 hover:bg-gray-400/50 cursor-pointer transition">
                            {{ $item->{$displayColumn} }}
                        </li>
                    @endforeach
                </ul> --}}
                <div class="flex flex-col">
                    @foreach ($results as $item)
                        <button type="button" wire:click="selectSuggestion({{ $item->id }})"
                            wire:keydown.enter="selectSuggestion({{ $item->id }})"
                            {{ $attributes->merge(['class' => $itemClass]) }} >
                            <span class="flex items-center gap-2 capitalize">{{ $item->{$displayColumn} }}</span>
                        </button>
                    @endforeach
                </div>
            @elseif(!$loading && strlen($value) > 2)
                <div
                    class="absolute left-0 right-0 mt-1 rounded-lg border border-gray-200 bg-white text-gray-500 p-2 text-sm z-10">
                    No hay resultados
                </div>
            @endif
        </div>


        @if ($error)
            <p class="text-sm text-red-600 mt-1">{{ $error }}</p>
        @endif

        {{-- Validation Error --}}
        @error($name)
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
