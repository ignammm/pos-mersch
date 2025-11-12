@props([
    'icon' => null,
    'color' => 'blue',
])

@php
    $sortOptionsSelect = [];
    if (count($sort_columns) > 0) {
        $columnLookup = [];
        foreach ($columns as $col) {
            $columnLookup[$col['name']] = $col['label'];
        }
        foreach ($sort_columns as $column) {
            if (isset($columnLookup[$column])) {
                $sortOptionsSelect[$column] = $columnLookup[$column];
            }
        }
    }
@endphp
<div class="space-y-4">
    <div class="space-y-2">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3">
                    @if ($icon)
                        <div
                            class="p-3 bg-gradient-to-br from-{{ $color }}-500 to-{{ $color }}-600 rounded-xl text-white shadow-lg">
                            <i class="fas {{ $icon }} text-2xl"></i>
                        </div>
                    @endif
                    <div>
                        <h1
                            class="text-3xl font-bold text-gray-900 bg-gradient-to-r from-{{ $color }}-600 to-purple-600 bg-clip-text text-transparent">
                            {{ $title }}
                        </h1>
                        <p class="text-gray-600 mt-1">{{ $subtitle }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <x-main-button icon='fas fa-plus' variant="primary" :label="'Crear ' . $item_name"
                        onclick="window.location.href='{{ route($route_name . '.create') }}'" />
                </div>
            </div>
        </div>

        {{-- Search --}}
        <x-card>
            @if (count($searchable_columns) > 0)
                <div class="flex items-center gap-y-2 flex-wrap">
                    <div class="flex-grow relative me-2 inline-flex group">
                        <x-main-input name="searchterm" icon="fa-magnifying-glass"
                            placeholder="{{ $search_placeholder }}" wire:model.debounce.500ms="search_term"
                            wire:keyup.debounce.500ms='callSearch' value="{{ $search_term }}"></x-main-input>
                        <button wire:click="clearSearch" wire:loading.attr="disabled"
                            class="hover:text-slate-600 dark:hover:text-slate-400 disabled:text-slate-500/20 disabled:dark:text-slate-400/20
               disabled:hover:text-slate-500/50 disabled:dark:hover:text-slate-400/50
               self-center absolute right-3 transition-colors grid place-items-center
               rounded-full text-xs text-slate-500/50 dark:text-slate-400/50
               {{ $this->clearButtonDisabled ? 'opacity-0' : 'opacity-100' }} transition-all">
                            <span wire:loading.remove wire:target="clearSearch, callSearch">
                                <i class="fas fa-xmark text-xl mr-2"></i>
                            </span>
                            <span wire:loading wire:target="clearSearch, callSearch">
                                <svg class="animate-spin h-5 w-5 text-slate-500/50 dark:text-slate-400/50"
                                    viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24">
                                    <path
                                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                        stroke="currentColor" stroke-width="5" stroke-linecap="round"
                                        stroke-linejoin="round" class="text-white">
                                    </path>
                                </svg>
                            </span>
                        </button>
                    </div>
                    <div class="flex flex-shrink w-1/4 min-w-32">
                        <livewire:searchableselect name="searchcolumn" name="search_column" error="algo"
                            :searchable="false" :value="$search_column" required="true" :options="$searchable_columns"
                            wire:model="search_column"></livewire:searchableselect>
                    </div>
                    {{-- Filters && Sort --}}
                    <div class="flex items-center justify-end gap-x-2 w-full">
                        <div class="flex flex-wrap items-stretch gap-2 w-auto ">
                            {{-- small screen sort --}}
                            <div class="sm:hidden">
                                <x-dropdown-menu position="right">
                                    <x-slot name="trigger">
                                        <div class="w-full sm:w-auto">
                                            <x-main-button icon="fa-up-down" variant="primary" icononly="true"
                                                title="Filtrar" class="w-full sm:w-auto" />
                                        </div>
                                    </x-slot>
                                    <x-slot name="title">
                                        Ordernar por
                                    </x-slot>
                                    <div class="flex flex-col gap-2 px-4 py-2">
                                        <div class="grow">
                                            <x-select name="sortby" required="true" :options="$sortOptionsSelect"
                                                wire:model="sortby"></x-select>
                                        </div>
                                    </div>
                                    <x-slot name="footer">
                                        <div class="grow space-y-2">
                                            <x-main-button icon="check" variant="primary" class="!w-full"
                                                label="Ordenar" wire:click="callSearch" isLoading="true" />
                                        </div>
                                    </x-slot>
                                </x-dropdown-menu>
                            </div>
                            <div class="sm:hidden">
                                <x-main-button variant="primary" wire:loading.attr="disabled" icon="fa-sort-down"
                                    icononly="true" isloading="true"
                                    class="{{ $sort_order === 'desc' ? 'rotate-180' : '' }} transition-transform ease-in-out"
                                    wire:click="setSortby('{{ $sortby ?? $sort_columns[0] }}')">
                                </x-main-button>
                            </div>
                            @error('activefilters')
                            @enderror
                            {{-- Filters --}}
                            @if (count($filters) > 0)
                                <x-dropdown-menu position="right">
                                    <x-slot name="trigger">
                                        <div class="w-full sm:w-auto">
                                            <x-main-button icon="fa-filter" variant="primary" icononly="true"
                                                title="Filtrar" class="w-full sm:w-auto" />
                                        </div>
                                    </x-slot>
                                    <x-slot name="title">
                                        Filtrar por
                                    </x-slot>
                                    <div class="flex flex-col gap-2 px-4 py-2">
                                        @foreach ($filters as $filter)
                                            <div class="grow">
                                                <x-filter-field :filter="$filter" :error="$errors->first('activefilters.' . $filter['name'])"
                                                    wireModel="activefilters.{{ $filter['name'] }}" />
                                            </div>
                                        @endforeach
                                    </div>
                                    <x-slot name="footer">
                                        <div class="grow space-y-2">
                                            <x-main-button icon="fa-check" variant="primary" class="!w-full"
                                                label="Filtrar" wire:click="filterRows" isLoading="true" />
                                            <x-main-button icon="fa-xmark" variant="tertiary" class="!w-full"
                                                label="Remover Filtros" wire:click="clearAllFilters"
                                                isLoading="true" />
                                        </div>
                                    </x-slot>
                                </x-dropdown-menu>
                            @endif
                            {{-- column hide/show --}}
                            <x-dropdown-menu position="right">
                                <x-slot name="trigger">
                                    <div class="relative inline-flex group w-full sm:w-auto">
                                        <x-main-button icon="fa-columns" variant="primary" icononly="true"
                                            title="Mostrar columnas" class="w-full sm:w-auto" />
                                        <span
                                            class="group-hover:bg-secondary/90 transition-colors absolute top-0.5 left-0.5 grid min-h-[24px] min-w-[24px] -translate-x-2/4 -translate-y-2/4 place-items-center rounded-full bg-indigo-500 py-1 px-1 text-xs text-white">
                                            {{ count(array_filter($columns, fn($column) => !in_array($column['name'], $invisibleColumns))) }}
                                        </span>
                                    </div>
                                </x-slot>
                                <x-slot name="title">
                                    Ocultar columnas
                                </x-slot>
                                <div class="flex flex-col gap-2 px-4 py-2">
                                    @foreach ($columns as $column)
                                        <div class="grow">
                                            <x-checkboxinput
                                                wire:change="toggleInvisibleColumn('{{ $column['name'] }}')"
                                                label="{{ $column['label'] ?? ucfirst(str_replace('_', ' ', $column)) }}"
                                                :disabled="count(
                                                    array_diff(array_column($columns, 'name'), $invisibleColumns),
                                                ) === 1 && !in_array($column['name'], $invisibleColumns)" :checked="in_array($column['name'], $invisibleColumns)" />
                                        </div>
                                    @endforeach
                                    @if ($show_actions && count($actions) > 0)
                                        <div class="grow">
                                            <x-checkboxinput wire:change="toggleInvisibleColumn('actions')"
                                                label="Acciones" :checked="in_array('actions', $invisibleColumns)" />
                                        </div>
                                    @endif
                                </div>
                                <x-slot name="footer">
                                    <div class="grow items-center flex flex-1 justify-center">
                                        <x-main-button icon="check" variant="primary" label="Mostrar todas"
                                            wire:click="toggleAllColumns()" isLoading="true" />
                                    </div>
                                </x-slot>
                            </x-dropdown-menu>
                        </div>
                    </div>
                </div>
                {{-- filter badges --}}
                @if (count($filters) > 0)
                    <div class="flex flex-row flex-wrap items-center place-self-end gap-2 mt-2">
                        @foreach ($activefilters as $key => $value)
                            @if (!blank($value))
                                <div
                                    class="flex flex-row gap-x-1 items-center justify-between bg-indigo-700/20 border border-indigo-700 p-1 rounded-md">
                                    <p class="text-sm text-indigo-700 font-semibold">
                                        {{ $key }}:
                                        {{ is_array($value) ? implode(', ', $value) : $value }}</p>
                                    <button class="text-sm text-indigo-600 me-1 hover:text-indigo-300"
                                        wire:click="clearFilter('{{ $key }}')">X</button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

            @endif
            {{-- Messages --}}
            <div class="fixed top-4 right-4 z-[60] space-y-2 max-w-sm w-full">
                @if (session()->has('bad') || session()->has('warn') || session()->has('info') || session()->has('ok'))
                    <div x-data x-init="setTimeout(() => {
                        @this.call('clearMessages')
                    }, 3000)">
                    </div>
                @endif
                @if (session()->has('ok'))
                    <x-toast :message="session('ok')" type="success" />
                @endif
                @if (session()->has('bad'))
                    <x-toast :message="session('bad')" type="error" />
                @endif
                @if (session()->has('warn'))
                    <x-toast :message="session('warn')" type="warning" />
                @endif
                @if (session()->has('info'))
                    <x-toast :message="session('info')" type="info" />
                @endif
            </div>
        </x-card>
        <div class="max-w-full">
            <div class="hidden sm:block overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 table-striped-columns">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach ($columns as $column)
                                @if (!in_array($column['name'], $invisibleColumns))
                                    @php
                                        $columnName = is_array($column) ? $column['name'] : $column;
                                    @endphp
                                    <th scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                        @if (in_array($column['name'], $sort_columns)) wire:click="setSortby('{{ $columnName }}')" @endif>
                                        <div class="flex items-center gap-1">
                                            @if (in_array($column['name'], $sort_columns))
                                                @if ($sortby === $columnName)
                                                    <i
                                                        class="fas fa-arrow-up {{ $sort_order === 'asc' ? 'rotate-180' : 'rotate-0' }} mr-1 text-gray-600 transition-transform ease-in-out"></i>
                                                @else
                                                    <i class="fas fa-arrows-up-down mr-1 text-gray-400"></i>
                                                @endif
                                            @endif
                                            <span
                                                class="{{ $sortby === $columnName ? 'text-gray-900' : 'text-gray-500' }}">
                                                {{ $column['label'] ?? ucfirst(str_replace('_', ' ', $columnName)) }}
                                            </span>
                                        </div>
                                    </th>
                                @endif
                            @endforeach

                            @if ($show_actions && $route_name && !in_array('actions', $invisibleColumns) && count($actions) > 0)
                                <th scope="col"
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($rows as $row)
                            <tr class="hover:bg-gray-50">
                                @foreach ($columns as $column)
                                    @if (!in_array($column['name'], $invisibleColumns))
                                        @php
                                            $columnName = is_array($column) ? $column['name'] : $column;
                                        @endphp
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">
                                            @if (isset($column['view']))
                                                @include(
                                                    'components.crud-table.columns.' . $column['view'],
                                                    [
                                                        'row' => $row,
                                                        'column' => $column,
                                                    ]
                                                )
                                            @else
                                                <div title="{{ data_get($row, $columnName, '-') }}">
                                                    {{ data_get($row, $columnName, '-') }}
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                @endforeach

                                @if ($show_actions && $route_name && !in_array('actions', $invisibleColumns))
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">
                                        <div class="flex gap-2 justify-center items-center">
                                            @if (in_array('view', $actions))
                                                <form action="{{ route($route_name . '.show', [$row['id']]) }}"
                                                    method="get">
                                                    <button type="submit" title="Detalle"
                                                        class="text-green-500 hover:text-green-700">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if (in_array('edit', $actions))
                                                <form action="{{ route($route_name . '.edit', [$row['id']]) }}"
                                                    method="get">
                                                    <button type="submit" title="Editar"
                                                        class="text-indigo-500 hover:text-indigo-700">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if (in_array('delete', $actions))
                                                <button type="button" title="Eliminar"
                                                    class="text-red-500 hover:text-red-700"
                                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion')"
                                                    wire:click="setSelectedItem({{ $row['id'] }})"
                                                    wire:loading.attr="disabled">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) + ($show_actions ? 1 : 0) }}">
                                    class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <i class="fas fa-file-invoice text-5xl mb-4"></i>
                                        <p class="text-lg font-medium">No hay datos disponibles</p>
                                        <p class="text-sm mt-1">Las ventas aparecerán aquí una vez que se realicen
                                            transacciones</p>
                                    </div>
                                    <button type="button"
                                        class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded"
                                        onclick="window.location.href='{{ route($route_name . '.create') }}'">
                                        <i class="fas fa-plus mr-2"></i>Crear nuevo registro
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="sm:hidden">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    @forelse ($rows as $row)
                        <div
                            class="p-4 md:p-6 border rounded-xl shadow-sm bg-white hover:shadow-md transition-shadow duration-200">
                            {{-- Main content --}}
                            <div class="space-y-3 md:space-y-4">
                                @foreach ($columns as $column)
                                    @if (!in_array($column['name'], $invisibleColumns))
                                        @if ($loop->first)
                                            <div
                                                class="flex flex-row sm:flex-row sm:justify-between sm:items-start gap-1 mb-3">
                                                {{-- Label --}}
                                                <p class="text-md font-bold text-gray-500 flex-shrink-0">
                                                    {{ $column['label'] ?? ucfirst(str_replace('_', ' ', $column['name'])) }}
                                                </p>

                                                {{-- Value - Mobile optimized --}}
                                                <div class="sm:text-right">
                                                    <p
                                                        class="text-gray-900 dark:text-gray-100 font-bold text-md md:text-base break-words">
                                                        {{ data_get($row, $column['name'], '-') }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Separator --}}
                                            @if (!$loop->last)
                                                <div class="border-t dark:border-t-2 border-gray-100 sm:hidden">
                                                </div>
                                            @endif
                                        @else
                                            <div
                                                class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1">
                                                {{-- Label --}}
                                                <p class="text-sm font-medium text-gray-500 flex-shrink-0">
                                                    {{ $column['label'] ?? ucfirst(str_replace('_', ' ', $column['name'])) }}
                                                </p>
                                                @if (isset($column['view']))
                                                    <div class="text-gray-900">
                                                        @include(
                                                            'components.crud-table.columns.' . $column['view'],
                                                            [
                                                                'row' => $row,
                                                                'column' => $column,
                                                            ]
                                                        )
                                                    </div>
                                                @else
                                                    {{-- Value - Mobile optimized --}}
                                                    <div class="sm:text-right">
                                                        <p
                                                            class="text-gray-900 font-medium text-sm md:text-base break-words">
                                                            {{ data_get($row, $column['name'], '-') }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Separator --}}
                                            @if (!$loop->last)
                                                <div class="border-t border-gray-100 sm:hidden">
                                                </div>
                                            @endif
                                        @endif
                                    @endif
                                @endforeach
                            </div>

                            {{-- Actions --}}
                            @if ($show_actions && $route_name && !in_array('actions', $invisibleColumns) && count($actions) > 0)
                                <div
                                    class="flex justify-center sm:justify-end gap-2 mt-4 md:mt-6 pt-4 border-t border-gray-100">
                                    @if (in_array('view', $actions))
                                        <x-main-button icon='eye' variant="primary" icononly="true"
                                            title="Detalle" size="sm"
                                            onclick="window.location.href='{{ route($route_name . '.show', [$row['id']]) }}'" />
                                    @endif

                                    @if (in_array('edit', $actions))
                                        <x-main-button icon='pencil' variant="secondary" icononly="true"
                                            title="Editar" size="sm"
                                            onclick="window.location.href='{{ route($route_name . '.edit', [$row['id']]) }}'" />
                                    @endif

                                    @if (in_array('delete', $actions))
                                        <x-main-button icon='trash' variant="danger" icononly="true"
                                            title="Eliminar" size="sm"
                                            wire:click="setSelectedItem({{ $row['id'] }})"
                                            wire:loading.attr="disabled" :isLoading="true" />
                                    @endif
                                </div>
                            @endif
                        </div>
                        {{-- Empty state --}}
                    @empty
                        <div class="text-center py-12 md:py-16 text-gray-500 dark:text-gray-400 col-span-full">
                            <div class="flex flex-col items-center space-y-3">
                                <svg viewBox="0 0 256 256" class=" w-16 h-16 text-gray-300 dark:text-gray-600"
                                    style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:1.41421;"
                                    version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    fill="#000000">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <path
                                            d="M191.33,62.954c1.527,0.181 1.928,0.415 2.664,0.866c3.049,1.868 3.769,6.697 1.107,9.358c-1.111,1.112 -2.657,1.695 -4.242,1.758l-96.868,0c-1.537,-0.061 -1.955,-0.262 -2.724,-0.654c-3.327,-1.695 -4.297,-6.811 -1.519,-9.589c1.111,-1.111 2.657,-1.695 4.243,-1.757l96.868,0c0.157,0.006 0.314,0.012 0.471,0.018Z"
                                            style="fill:currentColor;fill-rule:nonzero;"></path>
                                        <path
                                            d="M136.145,94.956c5.011,0.395 7.919,8.167 2.686,11.188c-0.635,0.367 -1.337,0.616 -2.062,0.73c-0.31,0.049 -0.624,0.066 -0.938,0.074l-41.84,0c-5.159,-0.135 -8.337,-8.115 -3,-11.196c0.635,-0.367 1.337,-0.615 2.061,-0.73c0.31,-0.049 0.624,-0.066 0.939,-0.074l41.84,0c0.105,0.003 0.209,0.006 0.314,0.008Z"
                                            style="fill:currentColor;fill-rule:nonzero;"></path>
                                        <path
                                            d="M191.128,94.954c4.424,0.299 7.487,6.236 4.249,9.942c-1.123,1.285 -2.793,2.013 -4.518,2.052l-30.277,0c-4.361,-0.098 -7.649,-5.68 -4.854,-9.527c1.11,-1.526 2.945,-2.43 4.854,-2.473l30.277,0c0.09,0.002 0.179,0.004 0.269,0.006Z"
                                            style="fill:currentColor;fill-rule:nonzero;"></path>
                                        <path
                                            d="M191.128,155.586c4.461,0.3 7.443,6.286 4.249,9.941c-1.123,1.286 -2.793,2.014 -4.518,2.053l-30.277,0c-4.548,-0.102 -7.823,-6.165 -4.518,-9.948c1.123,-1.285 2.793,-2.014 4.518,-2.052l30.277,0c0.09,0.002 0.179,0.004 0.269,0.006Z"
                                            style="fill:currentColor;fill-opacity:0.290196;fill-rule:nonzero;"></path>
                                        <path
                                            d="M136.281,155.588c4.251,0.335 7.268,5.763 4.54,9.518c-1.108,1.525 -2.951,2.424 -4.854,2.474l-41.976,0c-5.115,-0.134 -8.274,-8.152 -3,-11.196c0.635,-0.367 1.337,-0.616 2.061,-0.73c0.31,-0.05 0.624,-0.066 0.939,-0.074l41.976,0c0.105,0.002 0.209,0.005 0.314,0.008Z"
                                            style="fill:currentColor;fill-rule:nonzero;"></path>
                                        <path
                                            d="M191.26,124.725c4.371,0.44 7.291,6.181 4.181,9.86c-1.12,1.325 -2.225,2.048 -4.582,2.126l-68.019,0c-4.558,-0.152 -7.78,-6.091 -4.582,-9.873c1.12,-1.325 2.224,-2.048 4.582,-2.127l68.019,0c0.133,0.005 0.267,0.009 0.401,0.014Z"
                                            style="fill:currentColor;fill-opacity:0.290196;fill-rule:nonzero;"></path>
                                        <path
                                            d="M100.706,199.292c-24.359,0.84 -48.585,2.928 -72.448,6.347c-1.556,0.222 -3.733,0.54 -3.733,0.54c-11.662,1.38 -23.352,-8.08 -23.607,-20.551c-0.289,-42.436 -0.002,-84.769 0.003,-127.122l0.002,-14.221c0,0 0.128,-4.078 1.299,-7.251c3.154,-8.542 12.267,-14.419 21.588,-13.636c66.139,6.435 133.708,6.73 200.217,0.695c2.744,-0.249 5.486,-0.518 8.227,-0.776c0,0 0.702,-0.094 2.21,-0.076c10.783,0.265 20.396,9.542 20.618,20.719c0.309,47.21 0.003,94.335 -0.011,141.532c0,0 -0.134,4.181 -1.363,7.421c-3.307,8.722 -12.862,14.558 -22.376,13.365c-26.207,-3.649 -52.741,-5.999 -79.307,-6.943l0.001,0.044l0,5.878c15.996,2.139 31.752,6.107 46.937,11.438c0,0 4.243,1.819 4.555,5.332c0.319,3.603 -3.267,7.006 -6.871,6.463c-1.716,-0.259 -1.996,-0.591 -2.79,-0.868c-42.618,-14.74 -89.902,-16.767 -133.209,0.508c0,0 -3.387,0.814 -5.473,-0.53c-3.702,-2.386 -3.549,-8.8 1.549,-10.825c14.217,-5.646 28.976,-9.52 43.979,-11.576l0,-5.82l0.003,-0.087Zm133.305,-164.054c-6.231,0.092 -13.359,1.223 -20.194,1.774c-60.569,4.884 -121.882,4.784 -182.565,-0.838c-4.004,-0.371 -8.257,-1.521 -11.787,-0.521c-3.694,1.046 -6.463,4.625 -6.541,8.533c-0.312,47.102 -0.328,94.181 -0.007,141.278c0.103,5.025 4.817,9.434 10.116,8.807c17.904,-2.367 35.924,-4.614 54.005,-5.87c50.801,-3.53 101.921,-1.466 152.431,5.499c6.12,0.844 13.448,-0.767 13.602,-8.306c0.316,-47.083 0.321,-94.277 0.012,-141.476c-0.092,-4.603 -3.973,-8.697 -8.718,-8.876c-0.118,-0.003 -0.236,-0.004 -0.354,-0.004Z"
                                            style="fill:currentColor;fill-rule:nonzero;"></path>
                                        <path
                                            d="M80.029,64.248c0,-0.778 -0.309,-1.523 -0.858,-2.073c-0.55,-0.55 -1.296,-0.859 -2.073,-0.859c-2.612,0 -6.414,0 -9.025,0c-0.778,0 -1.523,0.309 -2.073,0.859c-0.55,0.55 -0.859,1.295 -0.859,2.073c0,2.698 0,6.678 0,9.376c0,0.777 0.309,1.523 0.859,2.073c0.55,0.549 1.295,0.858 2.073,0.858c2.611,0 6.413,0 9.025,0c0.777,0 1.523,-0.309 2.073,-0.858c0.549,-0.55 0.858,-1.296 0.858,-2.073c0,-2.698 0,-6.678 0,-9.376Z"
                                            style="fill:currentColor;"></path>
                                        <path
                                            d="M80.029,96.26c0,-0.777 -0.309,-1.523 -0.858,-2.073c-0.55,-0.55 -1.296,-0.858 -2.073,-0.858c-2.612,0 -6.414,0 -9.025,0c-0.778,0 -1.523,0.308 -2.073,0.858c-0.55,0.55 -0.859,1.296 -0.859,2.073c0,2.698 0,6.678 0,9.376c0,0.778 0.309,1.523 0.859,2.073c0.55,0.55 1.295,0.859 2.073,0.859c2.611,0 6.413,0 9.025,0c0.777,0 1.523,-0.309 2.073,-0.859c0.549,-0.55 0.858,-1.295 0.858,-2.073c0,-2.698 0,-6.678 0,-9.376Z"
                                            style="fill:currentColor;"></path>
                                        <path
                                            d="M80.029,156.892c0,-0.778 -0.309,-1.523 -0.858,-2.073c-0.55,-0.55 -1.296,-0.859 -2.073,-0.859c-2.612,0 -6.414,0 -9.025,0c-0.778,0 -1.523,0.309 -2.073,0.859c-0.55,0.55 -0.859,1.295 -0.859,2.073c0,2.698 0,6.678 0,9.376c0,0.777 0.309,1.523 0.859,2.073c0.55,0.549 1.295,0.858 2.073,0.858c2.611,0 6.413,0 9.025,0c0.777,0 1.523,-0.309 2.073,-0.858c0.549,-0.55 0.858,-1.296 0.858,-2.073c0,-2.698 0,-6.678 0,-9.376Z"
                                            style="fill:currentColor;"></path>
                                        <path
                                            d="M108.879,126.023c0,-0.777 -0.309,-1.523 -0.859,-2.072c-0.55,-0.55 -1.295,-0.859 -2.073,-0.859c-2.612,0 -6.413,0 -9.025,0c-0.777,0 -1.523,0.309 -2.073,0.859c-0.55,0.549 -0.858,1.295 -0.858,2.072c0,2.698 0,6.679 0,9.376c0,0.778 0.308,1.524 0.858,2.073c0.55,0.55 1.296,0.859 2.073,0.859c2.612,0 6.413,0 9.025,0c0.778,0 1.523,-0.309 2.073,-0.859c0.55,-0.549 0.859,-1.295 0.859,-2.073c0,-2.697 0,-6.678 0,-9.376Z"
                                            style="fill:currentColor;fill-opacity:0.290196;"></path>
                                    </g>
                                </svg>
                                <p class="text-lg font-medium text-gray-500 dark:text-gray-400">No hay datos
                                    disponibles
                                </p>
                                <x-main-button icon="plus" class="mt-1 text-sm" variant="primary"
                                    onclick="window.location.href='{{ route($route_name . '.create') }}'"
                                    label="Crear nuevo registro" />
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <!--Delete modal-->
        <x-main-modal name="confirm-deletion" focusable>
            <div class="flex space-y-4 flex-col items-center px-5 py-6 border-b border-gray-200">
                {!! $deleteModalContent !!}
                <div class="flex items-center justify-center gap-x-4">
                    <x-main-button label="Cancelar" variant="secondary" wire:click='setSelectedItem(null)'
                        x-on:click="$dispatch('close')" wire:target='deleteSelected' wire:loading.attr="disabled" />
                    <x-main-button disabled="{{ $selectedItem === null }}" label="Eliminar" variant="danger"
                        wire:click="deleteSelected" :isLoading="true" wire:loading.attr="disabled" />
                </div>
            </div>
        </x-main-modal>
        <!-- Pagination -->
        <x-card>
            <div class="flex sm:flex-row items-center">
                <p
                    class="text-slate-800 bg-white block max-w-fit text-nowrap px-3 py-3 border rounded-md rounded-r-none shadow-sm transition-colors">
                    Items por pagina</p>
                <x-select name="per_page" class=" max-w-20 rounded-l-none" required="true"
                    wire:change='setItemsPerPage' :options="$itemsPerPageOptions" wire:model="itemsPerPage"></x-select>
            </div>
            @php
                $currentPage = (int) $currentPage;
                $totalPages = (int) $totalPages;

                $startPage = max(1, $currentPage - floor($maxVisiblePages / 2));
                $endPage = min($totalPages, $startPage + $maxVisiblePages - 1);
                $startPage = max(1, $endPage - $maxVisiblePages + 1);

                $showLeftEllipsis = $startPage > 1;
                $showRightEllipsis = $endPage < $totalPages;
            @endphp
            @if ($showPagination)

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
                    <!-- Pagination Info -->
                    <div class="text-sm text-gray-600">
                        Página {{ $currentPage }} de {{ $totalPages }}
                    </div>

                    <!-- Pagination buttons -->
                    <nav class="flex items-center space-x-2">
                        <!-- Previous Page Link -->
                        <x-main-button icon='fa-arrow-left' icononly="true" title="Anterior" iconsize="small"
                            wire:click="changePage('{{ $currentPage - 1 }}')" wire:loading.attr="disabled"
                            :isLoading="true" :disabled="$currentPage <= 1" />

                        <!-- First Page (if needed) -->
                        @if ($showLeftEllipsis)
                            <x-main-button icon='fa-arrow-left' icononly="true" title="Primera"
                                wire:click="changePage('1')" wire:loading.attr="disabled" :isLoading="true"
                                :disabled="$startPage <= 1" />

                            @if ($startPage > 2)
                                <span
                                    class="min-h-[38px] min-w-[38px] py-2 px-1 inline-flex justify-center items-center text-sm text-gray-500">
                                    ...
                                </span>
                            @endif
                        @endif

                        <!-- Page Numbers -->
                        @for ($page = $startPage; $page <= $endPage; $page++)
                            <x-main-button label="{{ $page }}"
                                wire:click="changePage('{{ $page }}')" wire:loading.attr="disabled"
                                :isLoading="true" :disabled="$page == $currentPage" />
                        @endfor

                        <!-- Last Page (if needed) -->
                        @if ($showRightEllipsis)
                            @if ($endPage < $totalPages - 1)
                                <span
                                    class="min-h-[38px] min-w-[38px] py-2 px-1 inline-flex justify-center items-center text-sm text-gray-500">
                                    ...
                                </span>
                            @endif

                            <x-main-button title="{{ $totalPages }}"
                                wire:click="changePage('{{ $totalPages }}')" wire:loading.attr="disabled"
                                :isLoading="true" :disabled="$endPage >= $totalPages" />
                        @endif

                        <!-- Next Page Link -->
                        <x-main-button icon='fa-arrow-right' icononly="true" title="Siguiente" iconsize="small"
                            wire:click="changePage('{{ $currentPage + 1 }}')" wire:loading.attr="disabled"
                            :isLoading="true" :disabled="$currentPage >= $totalPages" />
                    </nav>
                </div>
            @endif
        </x-card>
    </div>
