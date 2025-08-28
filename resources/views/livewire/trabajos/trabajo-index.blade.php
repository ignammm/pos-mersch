<div class="max-w-7xl mx-auto">

    <p class="text-4xl font-bold text-gray-900 pb-3">Trabajos</p>

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
