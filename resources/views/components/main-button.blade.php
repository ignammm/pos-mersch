@props([
    'label' => null,
    'type' => 'button',
    'icon' => null,
    'variant' => 'primary', // primary, secondary, tertiary, success, danger, warning, info
    'outline' => false,
    'isLoading' => false,
    'disabled' => false,
    'icononly' => false,
    'iconsize' => 'medium',
    'pill' => false,
    'iconClass' => '',
])

@php
    $variantColors = [
        'primary' => 'green',
        'secondary' => 'indigo',
        'tertiary' => 'gray',
        'success' => 'orange',
        'danger' => 'red',
        'warning' => 'yellow',
        'info' => 'blue',
    ];

    $color = $variantColors[$variant] ?? $variantColors['primary'];

    $variantClasses = $outline
        ? "border border-{$color}-500 text-{$color}-600 hover:bg-{$color}-50 focus:ring-{$color}-400"
        : "bg-gradient-to-br from-{$color}-500 to-{$color}-600 hover:from-{$color}-600 hover:to-{$color}-700 text-white focus:ring-{$color}-500";

    $sizes = [
        'tiny' => 'text-xs',
        'small' => 'text-sm',
        'medium' => 'text-md',
        'large' => 'text-lg',
        'xlarge' => 'text-xl',
        'xxlarge' => 'text-2xl',
        'xxxlarge' => 'text-3xl',
    ];

    $baseClasses = collect([
        'inline-flex items-center justify-center font-medium shadow-sm gap-2',
        'transition-all duration-150 ease-in-out',
        'focus:outline-none focus:ring-2 focus:ring-offset-2',
        'active:translate-y-1 active:scale-95',
        'disabled:opacity-50 disabled:cursor-not-allowed',
        'disabled:active:scale-100 disabled:active:translate-y-0',
        'hover:shadow-md hover:scale-105',
        $pill ? 'rounded-full px-3 py-2' : 'rounded-xl px-4 py-3',
        $variantClasses,
    ])->implode(' ');
@endphp

<button type="{{ $type }}" @if ($disabled) disabled @endif
    {{ $attributes->merge(['class' => $baseClasses]) }}>
    {{-- Spinner when loading --}}
    @if ($isLoading)
        <svg wire:loading wire:target="{{ $attributes->get('wire:click') }}"
            class="h-6 w-6 {{ $icononly ? '' : 'mr-2' }} animate-spin text-white/50" viewBox="0 0 64 64"
            fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3Z"
                stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
            <path
                d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"
                class="text-white"></path>
        </svg>
    @endif

    {{-- Icon --}}
    @if ($icon)
        <i @if ($isLoading) wire:loading.remove @endif wire:target="{{ $attributes->get('wire:click') }}"
            class="fas {{ $sizes[$iconsize] }}  {{ $icon }} {}"></i>
    @endif

    {{-- Label --}}
    @if (!$icononly)
        <span wire:target="{{ $attributes->get('wire:click') }}">
            {{ $label ?? $slot }}
        </span>
    @endif
</button>
