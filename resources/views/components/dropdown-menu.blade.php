@props([
    'width' => 'md', // sm, md, lg, xl
    'position' => 'left', // left, right, center
])

@php
    $theme = [
        'bg' => 'bg-white',
        'border' => 'border-gray-200',
        'text' => 'text-slate-900',
        'textSecondary' => 'text-slate-800',
        'hover' => 'hover:text-gray-600',
        'overlay' => 'bg-gray-600 bg-opacity-50',
        'shadow' => 'shadow-lg',
    ];

    $widths = [
        'sm' => 'w-56',
        'md' => 'w-64',
        'lg' => 'w-72',
        'xl' => 'w-80',
    ];
@endphp

<div x-data="{ open: false }" x-on:close.stop="open = false" class="relative inline-block text-left">
    {{-- Trigger --}}
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    {{-- Desktop Dropdown --}}
    <div x-show="open" x-transition.origin.top.left @click.away="open = false"
        class="hidden sm:block absolute {{ $widths[$width] }} max-h-80 overflow-auto mt-2
            {{ $theme['bg'] }} {{ $theme['border'] }} {{ $theme['shadow'] }}
            rounded-md ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
        :class="{
            'left-0': '{{ $position }}'
            === 'left',
            'right-0': '{{ $position }}'
            === 'right',
            'left-1/2 -translate-x-1/2': '{{ $position }}'
            === 'center'
        }"
        x-cloak>

        {{-- Header --}}
        <div class="flex flex-row items-center {{ $theme['border'] }} border-b px-6 py-3">
            @isset($title)
                <div class="text-sm font-medium {{ $theme['text'] }}">
                    {{ $title }}
                </div>
            @endisset
            <div class="absolute right-4 flex items-center my-6">
                <button @click="open = false" class="text-md {{ $theme['textSecondary'] }}">X</button>
            </div>
        </div>

        {{-- Body --}}
        <div class="py-1">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @isset($footer)
            <div class="{{ $theme['border'] }} border-t px-4 py-2">
                {{ $footer }}
            </div>
        @endisset
    </div>

    {{-- Mobile Modal --}}
    <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 sm:hidden" x-cloak>
        {{-- Backdrop --}}
        <div x-show="open" x-transition.opacity class="fixed inset-0 {{ $theme['overlay'] }}" @click="open = false">
        </div>

        {{-- Modal Panel --}}
        <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div
                class="{{ $theme['bg'] }} {{ $theme['shadow'] }} {{ $theme['border'] }}
                rounded-lg w-full max-w-sm max-h-[80vh] overflow-hidden flex flex-col pointer-events-auto">

                {{-- Header --}}
                <div class="flex justify-between items-center {{ $theme['border'] }} border-b px-6 py-4">
                    @isset($title)
                        <div class="text-lg font-semibold {{ $theme['text'] }}">
                            {{ $title }}
                        </div>
                    @else
                        <div></div>
                    @endisset
                    <button @click="open = false"
                        class="text-2xl {{ $theme['textSecondary'] }} {{ $theme['hover'] }} transition-colors">
                        &times;
                    </button>
                </div>

                {{-- Body --}}
                <div class="flex-1 overflow-y-auto py-2">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                @isset($footer)
                    <div class="{{ $theme['border'] }} border-t px-4 py-3">
                        {{ $footer }}
                    </div>
                @endisset
            </div>
        </div>
    </div>
</div>
