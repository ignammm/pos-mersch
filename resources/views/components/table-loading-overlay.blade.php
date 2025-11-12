@props([
    'loading' => false,
    'message' => 'Cargando...',
    'spinnerSize' => 'w-12 h-12',
])

<div class="relative">
    {{-- Loading overlay - covers only tbody --}}
    <div x-show="{{ $loading ? 'true' : 'false' }}" x-transition.opacity
        class="absolute inset-0 bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm flex items-center justify-center z-50 rounded-lg"
        style="display: none;">
        <div class="flex flex-col items-center gap-3">
            {{-- Spinner --}}
            <svg class="{{ $spinnerSize }} animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>

            {{-- Loading message --}}
            @if ($message)
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $message }}</p>
            @endif
        </div>
    </div>

    {{-- Tbody content --}}
    {{ $slot }}
</div>
