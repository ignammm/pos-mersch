<div class="space-y-6">
    
    <!-- Header mejorado con icono y búsqueda -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl text-white shadow-lg">
                <i class="fas fa-receipt text-xl"></i>
            </div>
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Listado de Ventas</h2>
                <p class="text-gray-600 text-sm mt-1">Gestión y consulta de transacciones comerciales</p>
            </div>
        </div>
        
    </div>

    <!-- Filtros mejorados -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <div>
                <label for="nombreCliente" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-2 text-gray-400"></i>
                    Cliente
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="nombreCliente" 
                        wire:model.live="nombreCliente" 
                        placeholder="Buscar cliente..."
                        class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-box text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div>
                <label for="fechaDesde" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                    Fecha Desde
                </label>
                <input 
                    type="date" 
                    id="fechaDesde" 
                    wire:model.live="fechaDesde" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                >
            </div>

            <div>
                <label for="fechaHasta" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                    Fecha Hasta
                </label>
                <input 
                    type="date" 
                    id="fechaHasta" 
                    wire:model.live="fechaHasta" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                >
            </div>

        </div>
    </div>
 
    <!-- Tabla mejorada -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($ventas as $venta)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out table-row">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-primary-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $venta->cliente->nombre ?? 'Consumidor Final' }}</div>
                                        <div class="text-sm text-gray-500">Venta #{{ $venta->numero }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</div>
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($venta->fecha)->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">${{ number_format($venta->monto_original, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $configEstados = [
                                        'pendiente' => [
                                            'clases' => 'bg-yellow-100 text-yellow-800',
                                            'texto' => 'Pendiente',
                                            'icono' => 'clock'
                                        ],
                                        'parcial' => [
                                            'clases' => 'bg-blue-100 text-blue-800', 
                                            'texto' => 'Pago Parcial',
                                            'icono' => 'money-bill-wave'
                                        ],
                                        'pagada' => [
                                            'clases' => 'bg-green-100 text-green-800',
                                            'texto' => 'Pagada',
                                            'icono' => 'check-circle'
                                        ],
                                        'moroso' => [
                                            'clases' => 'bg-red-100 text-red-800',
                                            'texto' => 'Moroso',
                                            'icono' => 'exclamation-triangle'
                                        ],
                                        'anulada' => [
                                            'clases' => 'bg-gray-100 text-gray-800',
                                            'texto' => 'Anulada', 
                                            'icono' => 'ban'
                                        ]
                                    ];
                                    
                                    // CORRECCIÓN: Usar el estado real de la venta
                                    $estadoVenta = $venta->estado ?? 'pendiente';
                                    $config = $configEstados[$estadoVenta] ?? $configEstados['pendiente'];
                                @endphp
                                <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full {{ $config['clases'] }}">
                                    <i class="fas fa-{{ $config['icono'] }} mr-1 text-xs"></i>
                                    {{ $config['texto'] }}
                                </span>
                            </td>
                            <td class="flex px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <button 
                                    wire:click="verDetalle({{ $venta->id }})" 
                                    class="text-primary-600 hover:text-primary-900 bg-primary-50 hover:bg-primary-100 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2 mx-auto"
                                >
                                    <i class="fas fa-eye"></i>
                                    Ver Detalles
                                </button>
                                <!-- En la columna de acciones, actualiza el botón de pagar -->
                                @if($venta->estado !== 'pagada')
                                <button 
                                    wire:click="ingresarPago({{ $venta->id }})" 
                                    class="text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-lg font-medium transition-all duration-200 flex items-center gap-2"
                                >
                                    <i class="fas fa-credit-card"></i>
                                    Pagar
                                </button>
                                @else
                                <span class="text-gray-400 bg-gray-100 px-3 py-1 rounded-lg text-sm">
                                    <i class="fas fa-check"></i>
                                    Pagada
                                </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-receipt text-5xl mb-4"></i>
                                    <p class="text-lg font-medium">No hay ventas registradas</p>
                                    <p class="text-sm mt-1">Las ventas aparecerán aquí una vez que se realicen transacciones</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación mejorada -->
    <div class="items-center justify-between mt-6">
        <div class="bg-white px-4 py-3 rounded-lg shadow-sm border">
            {{ $ventas->links() }}
        </div>
    </div>

    <!-- Modal de detalles mejorado -->
    @if($mostrarDetalle && $facturaSeleccionada)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 fade-in">
            <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden slide-in">
                
                <!-- Contenido del modal -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <!-- Lista de productos -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Productos Vendidos</h4>
                        <div class="space-y-3">
                            @foreach($facturaSeleccionada->detalles as $detalle)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $detalle->articulo->articulo ?? 'Artículo eliminado' }}</p>
                                        <div class="flex gap-4 mt-1 text-sm text-gray-600">
                                            <span>Marca: {{ $detalle->articulo->marca ?? 'N/A' }}</span>
                                            <span>Cantidad: {{ $detalle->cantidad }}</span>
                                            <span>Precio: ${{ number_format($detalle->precio_unitario, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900">${{ number_format($detalle->subtotal, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Resumen de la venta -->
                    <div class="border-t pt-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal:</span>
                                <span>${{ number_format($facturaSeleccionada->monto_original, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Impuestos:</span>
                                <span>$0.00</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-gray-800 pt-2 border-t">
                                <span>Total:</span>
                                <span>${{ number_format($facturaSeleccionada->monto_original, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer del modal -->
                <div class="px-6 py-4 bg-gray-50 flex justify-end">
                    <button 
                        wire:click="cerrarDetalle" 
                        class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors duration-200 flex items-center gap-2"
                    >
                        <i class="fas fa-times"></i>
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif

    @include('pagos/partials/modal-pagar');

    <!-- Agregar estos estilos para mejorar la paginación -->
    <style>
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .page-item {
            margin: 0 2px;
        }
        
        .page-link {
            display: block;
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .page-link:hover {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .page-item.active .page-link {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }
        
        .page-item.disabled .page-link {
            color: #9ca3af;
            pointer-events: none;
            background-color: #f9fafb;
        }
    </style>
</div>
