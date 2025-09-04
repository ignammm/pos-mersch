<div class="max-w-7xl mx-auto">

    <p class="text-4xl font-bold text-gray-900 pb-3">Trabajos</p>

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
        <input
            type="text"
            wire:model.live="search"
            class="rounded border-gray-300 shadow-sm w-1/3"
            placeholder="Buscar trabajo, cliente o vehículo..."
        >

        <a href="{{ route('trabajos.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            + Nuevo Trabajo
        </a>
    </div>

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-left text-sm text-white">Nombre</th>
                    <th class="px-4 py-2 text-left text-sm text-white">Fecha</th>
                    <th class="px-4 py-2 text-left text-sm text-white">Cliente</th>
                    <th class="px-4 py-2 text-left text-sm text-white">Vehículo</th>
                    <th class="px-4 py-2 text-center text-sm text-white">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($trabajos as $trabajo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $trabajo->nombre }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($trabajo->fecha)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $trabajo->vehiculoCliente->cliente->nombre ?? '-' }}</td>
                        <td class="px-4 py-2">
                            {{ $trabajo->vehiculoCliente->vehiculo->marca ?? '' }}
                            {{ $trabajo->vehiculoCliente->vehiculo->modelo ?? '' }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('trabajos.show', $trabajo) }}"
                               class="text-blue-600 hover:underline text-sm">
                               Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            No se encontraron trabajos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $trabajos->links() }}
    </div>

</div>
