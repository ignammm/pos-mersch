<div class="space-y-6">
    <h2 class="text-4xl font-semibold">Listado de Ventas</h2>

    <table class="min-w-full bg-white border">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Cliente</th>
                <th class="px-4 py-2 text-left">Fecha</th>
                <th class="px-4 py-2 text-left">Total</th>
                <th class="px-4 py-2">Comprobante</th>
                <th class="px-4 py-2 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $venta)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $venta->cliente->nombre ?? 'Consumidor Final' }}</td>
                    <td class="px-4 py-2">{{ $venta->fecha }}</td>
                    <td class="px-4 py-2">${{ number_format($venta->monto_final, 2) }}</td>
                    <td class="px-4 py-2 text-center">{{ $venta->tipo_comprobante }} #{{ $venta->numero }}</td>
                    <td class="px-4 py-2 text-center">
                        <x-button wire:click="verDetalle({{ $venta->id }})">Ver Detalles</x-button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center text-gray-500">No hay ventas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $ventas->links() }}
    </div>

    @if($mostrarDetalle && $ventaSeleccionada)
        <div class="border p-4 bg-gray-50 rounded shadow">
            <h3 class="text-lg font-bold mb-2">
                Detalles de la venta #{{ $ventaSeleccionada->numero }}
            </h3>
            <ul class="space-y-1">
                @foreach($ventaSeleccionada->detalles as $detalle)
                    <li>
                        {{ $detalle->articulo->articulo ?? 'Artículo eliminado' }} -
                        Cantidad: {{ $detalle->cantidad }} -
                        Precio: ${{ number_format($detalle->precio_unitario, 2) }} -
                        Subtotal: ${{ number_format($detalle->subtotal, 2) }}
                    </li>
                @endforeach
            </ul>

            <div class="mt-3">
                <x-button wire:click="cerrarDetalle" color="gray">Cerrar</x-button>
            </div>
        </div>
    @endif
</div>
                    