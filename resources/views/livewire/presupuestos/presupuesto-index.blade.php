<div class="max-w-7xl mx-auto">

    <p class="text-4xl font-bold text-gray-900 pb-6">Presupuestos</p>

    @if(session('message'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-5 right-5 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 max-w-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('message') }}</span>
                </div>
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif


    <div class="flex justify-between mb-4">
        <!-- Este div ocupa la mayor parte del espacio -->
        <div class="w-3/4 flex gap-4 items-end">
            <!-- Buscar referencia -->
            <div class="flex flex-col w-1/2">
                <label for="search" class="text-sm font-medium text-gray-700 mb-1">Referencia</label>
                <input
                    id="search"
                    type="text"
                    wire:model.live="search"
                    class="rounded border-gray-300 shadow-sm w-full"
                    placeholder="Buscar referencia..."
                >
            </div>

            <!-- Fecha desde -->
            <div class="flex flex-col">
                <label for="fecha_inicio" class="text-sm font-medium text-gray-700 mb-1">Desde</label>
                <input
                    id="fecha_inicio"
                    type="date"
                    wire:model.live="search_fecha_inicio"
                    class="rounded border-gray-300 shadow-sm w-40"
                >
            </div>

            <!-- Fecha hasta -->
            <div class="flex flex-col">
                <label for="fecha_fin" class="text-sm font-medium text-gray-700 mb-1">Hasta</label>
                <input
                    id="fecha_fin"
                    type="date"
                    wire:model.live="search_fecha_fin"
                    class="rounded border-gray-300 shadow-sm w-40"
                >
            </div>
        </div>

        <!-- Botón nuevo presupuesto -->
        <a href="{{ route('presupuestos.create') }}"
        class="self-end bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            + Nuevo Presupuesto
        </a>
    </div>



    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-left text-sm text-white">Nombre</th>
                    <th class="px-4 py-2 text-left text-sm text-white">Fecha</th>
                    <th class="px-4 py-2 text-left text-sm text-white">Estado</th>
                    <th class="px-4 py-2 text-left text-sm text-white">Total</th>
                    <th class="px-4 py-2 text-center text-sm text-white">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($presupuestos as $presupuesto)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2" title="{{ $presupuesto->observaciones }}">
                            {{ \Illuminate\Support\Str::limit($presupuesto->observaciones, 50, '...') }}
                        </td>   
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($presupuesto->fecha_emision)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">
                            @if ($presupuesto->estado === 'pendiente')
                                <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">
                                    Pendiente
                                </span>
                            @elseif ($presupuesto->estado === 'aceptado')
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">
                                    Aceptado
                                </span>
                            @elseif ($presupuesto->estado === 'rechazado')
                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-full">
                                    Rechazado
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">
                                    {{ ucfirst($presupuesto->estado) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2">${{  number_format($presupuesto->subtotal, 2, ',', '.') }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('presupuestos.show', $presupuesto) }}"
                               class="text-blue-600 hover:underline text-sm">
                               Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            No se encontraron presupuestos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $presupuestos->links() }}
    </div>

</div>
