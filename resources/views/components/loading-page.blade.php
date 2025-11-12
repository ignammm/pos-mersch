@props([
    'message' => 'Cargando...',
    'icon' => 'fas fa-spinner',
    'variant' => 'overlay', // overlay | inline
    'color' => 'blue', // supports Tailwind color names
])

@php
    $containerClasses =
        $variant === 'overlay'
            ? 'fixed inset-0 z-50 flex flex-col items-center justify-center bg-white/80 dark:bg-gray-900/90 backdrop-blur-sm'
            : 'absolute inset-0 flex flex-col items-center justify-center bg-white/70 dark:bg-gray-900/70 backdrop-blur-sm rounded-lg';

    $spinnerColor = "border-{$color}-500 border-t-transparent";
    $iconColor = "text-{$color}-500";
@endphp

<div data-loading-page class="{{ $containerClasses }}">
    <!-- Spinner -->
    <div class="flex flex-col items-center justify-center space-y-4 animate-pulse">
        <div class="relative flex items-center justify-center">
            <div class="w-14 h-14 border-4 {{ $spinnerColor }} rounded-full animate-spin"></div>
        </div>

        <!-- Message -->
        <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300 text-lg font-semibold">
            @if ($icon)
                <i class="{{ $icon }} fa-spin {{ $iconColor }}"></i>
            @endif
            <span>{{ $message }}</span>
        </div>
    </div>
</div>
