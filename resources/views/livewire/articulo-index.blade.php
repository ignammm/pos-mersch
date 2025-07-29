<div class="max-w-7xl">

    <p class="text-5xl font-bold text-gray-900 dark:text-black pb-3">Articulos</p>

    <div class="flex justify-between mb-4">
        <input
            type="text"
            wire:model.debounce.300ms="search"
            class="rounded border-gray-300 shadow-sm"
            placeholder="Buscar artículo..."
        >

        <a href="{{ route('articulos.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white  px-4 py-2 rounded">
            + Nuevo Artículo        
        </a>
    </div>

    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Articulo</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Código Proveedor</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Stock</th>
                    <th class="px-4 py-2 text-right text-sm text-gray-700">Precio</th>
                    <th class="px-4 py-2 text-center text-sm text-gray-700">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($articulos as $articulo)
                    <tr>
                        <td class="px-4 py-2">{{ $articulo->articulo }}</td>
                        <td class="px-4 py-2">{{ $articulo->codigo_proveedor }}</td>
                        <td class="px-4 py-2">{{ $articulo->stock }}</td>
                        <td class="px-4 py-2 text-right">${{ number_format($articulo->precio, 2) }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('articulos.edit', $articulo) }}"
                               class="text-blue-600 hover:underline text-sm">
                               Editar
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
        {{ $articulos->links() }}
    </div>

</div>
