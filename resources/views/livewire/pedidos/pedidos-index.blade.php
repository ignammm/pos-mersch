<div class="space-y-6">
    <!-- Header mejorado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-green-500 rounded-lg text-white">
                <i class="fas fa-box-open text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Artículos Cargados</h1>
                <p class="text-gray-600 text-sm mt-1">Historial de artículos cargados en el sistema</p>
            </div>
        </div>
        
        <!-- Resumen rápido -->
        <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3">
            <div class="flex items-center gap-2">
                <i class="fas fa-chart-bar text-green-500"></i>
                <span class="text-sm font-medium text-green-800">
                    {{ $detalles->total() }} artículos encontrados
                </span>
            </div>
        </div>
    </div>

    

    <!-- Filtros mejorados -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <div>
                <label for="codigoArticulo" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-2 text-gray-400"></i>
                    Código/Nombre Artículo
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="codigoArticulo" 
                        wire:model.live="codigoArticulo" 
                        placeholder="Buscar artículo..."
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
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Artículo
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Marca
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cantidad
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hora
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subtotal
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($detalles as $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-green-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->articulo->articulo }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $item->articulo->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $item->articulo->marca }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800">
                                    {{ $item->cantidad }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                <div class="flex flex-col items-center">
                                    <span class="font-medium">{{ $item->created_at->format('H:i') }}</span>
                                    <span class="text-xs text-gray-500">{{ $item->created_at->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-semibold text-gray-900">
                                    ${{ number_format($item->detalle_venta->subtotal, 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-inbox text-5xl mb-4"></i>
                                    <p class="text-lg font-medium">No se encontraron artículos</p>
                                    <p class="text-sm mt-1">Ajusta los filtros para ver los resultados</p>
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
            {{ $detalles->links() }}
        </div>
    </div>
    <style>
    .table-row:hover {
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
    
    /* Mejoras para la paginación */
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 4px;
    }
    
    .page-item {
        margin: 0;
    }
    
    .page-link {
        display: block;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        color: #6b7280;
        text-decoration: none;
        transition: all 0.2s;
        min-width: 40px;
        text-align: center;
    }
    
    .page-link:hover {
        background-color: #f3f4f6;
        color: #374151;
        border-color: #d1d5db;
    }
    
    .page-item.active .page-link {
        background-color: #10b981;
        border-color: #10b981;
        color: white;
    }
    
    .page-item.disabled .page-link {
        color: #9ca3af;
        pointer-events: none;
        background-color: #f9fafb;
    }
    </style>
</div>
