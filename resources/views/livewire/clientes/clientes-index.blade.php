<div class="max-w-7xl">

    <p class="text-4xl font-bold text-gray-900 dark:text-black pb-3">Clientes</p>

    <div class="flex justify-between mb-4">
        <input
            type="text"
            wire:model.live="search"
            class="rounded border-gray-300 shadow-sm"
            placeholder="Buscar cliente..."
        >

        <a href="{{ route('clientes.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white  px-4 py-2 rounded">
            + Nuevo Cliente        
        </a>
    </div>

    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200 ">
            <thead class="bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-left text-sm text-white">Nombre</th>
                    <th class="px-4 py-2 text-left text-sm text-white">Telefono</th>
                    <th class="px-4 py-2 text-left text-sm text-white">Cuit</th>
                    <th class="px-4 py-2 text-left text-sm text-white">Tipo Cliente</th>
                    <th class="px-4 py-2 text-left text-sm text-white">Percepcion Iva</th>
                    <th class="px-4 py-2 text-center text-sm text-white">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($clientes as $cliente)
                    <tr>
                        <td class="px-4 py-2">{{ $cliente->nombre }}</td>
                        <td class="px-4 py-2">{{ $cliente->telefono }}</td>
                        <td class="px-4 py-2">{{ $cliente->cuit_cuil }}</td>
                        <td class="px-4 py-2">{{ $cliente->tipo_cliente }}</td>
                        <td class="px-4 py-2">{{ $cliente->percepcion_iva}}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('clientes.edit', $cliente) }}"
                               class="text-blue-600 hover:underline text-sm">
                               Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            No se encontraron artículos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $clientes->links() }}
    </div>

</div>
