@props(['message', 'type' => 'error'])

@php
    $variants = [
        'success' => 'bg-green-600 text-white',
        'error' => 'bg-red-600 text-white',
        'warning' => 'bg-yellow-500 text-white',
        'info' => 'bg-blue-600 text-white',
    ];

    $classes = $variants[$type] ?? $variants['info'];
@endphp

<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="flex items-center p-4 rounded-lg shadow-lg {{ $classes }}">
    <div class="flex items-center space-x-2">
        @if ($type === 'success')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        @elseif ($type === 'error')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        @elseif ($type === 'warning')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M12 5a7 7 0 100 14 7 7 0 000-14z" />
            </svg>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
        @endif

        <span class="font-medium">{{ $message }}</span>
    </div>
</div>
