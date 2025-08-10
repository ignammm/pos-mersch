<div>
    <h1 class="text-4xl font-bold mb-5">Reponer</h1>

    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Cantidad</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Articulo</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Marca</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Precio</th>
                    <th class="px-4 py-2 text-center text-sm text-gray-700">Cargar</th>
                    
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($detalles_venta as $item)
                    <tr>
                        
                        <td class="px-4 py-2">{{ $item->cantidad }}</td>
                        <td class="px-4 py-2">{{ $item->articulo->articulo }}</td>
                        <td class="px-4 py-2">{{ $item->articulo->marca }}</td>
                        <td class="px-4 py-2">${{ $item->articulo->precio }}</td>
                        <td class="px-4 py-2 text-center">
                           <input 
                                type="checkbox" 
                                wire:model='seleccionados'
                                value="{{ $item->id }}" 
                                {{ $item->repuesto == 1 ? 'checked' : '' }}
                            >
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
        {{ $detalles_venta->links() }}
    </div>
    
    <div class="text-left mt-3">
        <x-button wire:click="guardar" class="text-white px-4 py-2 rounded">
            Guardar Pedido
        </x-button>
    </div>
</div>
