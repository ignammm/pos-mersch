<div class="max-w-7xl">

    <p class="text-4xl font-bold text-gray-900 dark:text-black pb-3">Articulos</p>

    <div class="flex justify-between mb-4">
        <input
            type="text"
            wire:model.live="search"
            class="rounded border-gray-300 shadow-sm"
            placeholder="Buscar artículo..."
        >

        <a href="{{ route('articulos.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white  px-4 py-2 rounded">
            + Nuevo Artículo        
        </a>
    </div>

    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200 ">
            <thead class="bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-left text-sm text-white">Marca</th>
                    <th class="px-4 py-2 text-left text-sm text-white">Articulo</th>
                    <th class="px-4 py-2 text-left text-sm text-white">Rubro</th>
                    <th class="px-4 py-2 text-left text-sm text-white">Stock</th>
                    <th class="px-4 py-2 text-right text-sm text-white">Precio</th>
                    <th class="px-4 py-2 text-center text-sm text-white">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($articulos as $articulo)
                    <tr>
                        <td class="px-4 py-2">{{ $articulo->marca }}</td>
                        <td class="px-4 py-2">{{ $articulo->articulo }}</td>
                        <td class="px-4 py-2">{{ $articulo->rubro }}</td>
                        <td class="px-4 py-2">{{ $articulo->stock->cantidad ?? 0 }}</td>
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
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">
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
