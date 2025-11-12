@props([
    'label' => null,
    'name' => null,
    'placeholder' => 'Seleccionar...',
    'searchPlaceholder' => 'Buscar...',
    'icon' => '',
    'options' => [],
    'currentoptions' => [],
    'value' => null,
    'error' => null,
    'searchable' => true,
    'inModal' => false,
    'disabled' => false,
])

<div x-data="{
    expanded: @entangle('expanded'),
    focusSearch() {
        this.$nextTick(() => this.$refs.searchInput?.focus())
    }
}" @click.outside="expanded = false" class="relative w-full">
    {{-- Label --}}
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}
        </label>
    @endif

    {{-- Trigger button --}}
    <button @if ($disabled) disabled @endif type="button" id="{{ $name }}"
        @click="expanded = !expanded; if (expanded) focusSearch()"
        class="flex flex-row group items-center shadow-md justify-between w-full min-w-fit bg-white px-4 py-3 transition-all border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500
        disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
        :class="{ 'rounded-b-none': expanded, 'border-red-500': {{ $error ? 'true' : 'false' }} }">
        <div class="flex items-center">
            @if ($icon)
                <i class="fas {{ $icon }} text-gray-400 mr-3"></i>
            @endif
            <span class="text-gray-700 group-hover:text-gray-600">
                {{ $options[$value] ?? $placeholder }}
            </span>
        </div>

        <svg class="h-5 w-5 ml-2 transition-transform duration-200 text-gray-400 group-hover:text-gray-600"
            :class="{ 'rotate-180': expanded, 'rotate-0': !expanded }" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    {{-- Dropdown menu --}}
    <div x-show="expanded" x-transition.origin.top
        class="{{ $inModal ? '' : 'hidden' }} sm:block absolute w-full max-h-80 overflow-auto border border-gray-300 rounded-md rounded-t-none shadow-lg bg-gray-100 focus:outline-none z-50">
        @if ($searchable)
            <div class="flex flex-col md:flex-row gap-4 p-2">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" wire:model="searchterm" x-on:keydown.enter="expanded = false"
                            wire:keyup.debounce.150ms="search" x-ref="searchInput"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors rounded-md"
                            placeholder="{{ $searchPlaceholder }}">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex flex-col">
            @foreach ($currentoptions as $option => $optionLabel)
                <button type="button" wire:click="selectOption('{{ $option }}')"
                    class="flex flex-1 flex-row items-center gap-2 text-left px-4 py-2 hover:bg-blue-400/50 transition-colors {{ (string) $value === (string) $option ? 'bg-gray-400/50' : '' }}">
                    <span class="flex items-center gap-2 capitalize">{{ $optionLabel }}</span>
                    @if ((string) $value === (string) $option)
                        <i class="fas fa-check-circle text-green-500"></i>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- Mobile modal version --}}
    <div x-show="expanded" x-transition.opacity class="fixed inset-0 z-50 sm:hidden {{ $inModal ? 'hidden' : '' }}"
        x-cloak>
        {{-- Backdrop --}}
        <div x-show="expanded" x-transition.opacity class="fixed inset-0 bg-gray-600 bg-opacity-50"
            @click="expanded = false"></div>

        {{-- Modal --}}
        <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-sm max-h-[80vh] overflow-auto flex flex-col pointer-events-auto">
                <div class="flex flex-col md:flex-row gap-4 p-2">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" wire:model="searchterm" x-on:keydown.enter="expanded = false"
                                wire:keyup.debounce.150ms="search"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors rounded-md"
                                placeholder="{{ $placeholder }}">
                            <i
                                class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col">
                    @foreach ($currentoptions as $option => $optionLabel)
                        <button type="button" wire:click="selectOption('{{ $option }}')"
                            class="flex flex-1 flex-row items-center gap-2 text-left px-4 py-2 hover:bg-blue-400/50 transition-colors {{ (string) $value === (string) $option ? 'bg-gray-400/50' : '' }}">
                            <span class="flex items-center gap-2 capitalize">{{ $optionLabel }}</span>
                            @if ((string) $value === (string) $option)
                                <i class="fas fa-check-circle text-green-500"></i>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Error message --}}
    @if ($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
