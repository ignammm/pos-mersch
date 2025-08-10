<div>
    <h1 class="text-4xl font-bold mb-10">Articulos Cargados</h1>
    <div class="mb-4 flex gap-4 items-end">
        <div>
            <label for="fechaDesde">Desde</label>
            <input type="date" id="fechaDesde" wire:model.live="fechaDesde" class="form-input">
        </div>

        <div>
            <label for="fechaHasta">Hasta</label>
            <input type="date" id="fechaHasta" wire:model.live="fechaHasta" class="form-input">
        </div>

        <div>
            <label for="codigoArticulo">Código/Nombre artículo</label>
            <input type="text" id="codigoArticulo" wire:model.live="codigoArticulo" class="form-input">
        </div>
    </div>

    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="border px-2 py-1">Artículo</th>
                <th class="border px-2 py-1">Cantidad</th>
                <th class="border px-2 py-1">Hora</th>
                <th class="border px-2 py-1">Precio</th>
                <th class="border px-2 py-1">Repuesto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detalles as $item)
                <tr>
                    <td class="border px-2 py-1">{{ $item->articulo->articulo }}</td>
                    <td class="border px-2 py-1 text-center">{{ $item->cantidad }}</td>
                    <td class="border px-2 py-1 text-center">{{ $item->created_at->format('H:i') }}</td>
                    <td class="border px-2 py-1 text-center">${{ number_format($item->precio_unitario, 2) }}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="checkbox" disabled {{ $item->repuesto ? 'checked' : '' }}>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-2">No hay resultados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $detalles->links() }}
    </div>
</div>
