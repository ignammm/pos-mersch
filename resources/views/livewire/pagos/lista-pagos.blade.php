<!-- resources/views/livewire/pagos/lista-pagos.blade.php -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-500 rounded-lg text-white">
                <i class="fas fa-credit-card text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Gestión de Pagos</h1>
                <p class="text-gray-600 text-sm mt-1">Historial de todos los pagos del sistema</p>
            </div>
        </div>
        
        <!-- Resumen -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
            <div class="flex items-center gap-2">
                <i class="fas fa-chart-bar text-blue-500"></i>
                <span class="text-sm font-medium text-blue-800">
                    {{ $pagos->total() }} pagos encontrados
                </span>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-2 text-gray-400"></i>
                    Buscar
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live="search" 
                        placeholder="Buscar por referencia o venta..."
                        class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-filter mr-2 text-gray-400"></i>
                    Registros por página
                </label>
                <select wire:model.live="perPage" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <option value="10">10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sort mr-2 text-gray-400"></i>
                    Ordenar por
                </label>
                <select wire:model.live="sortField" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <option value="fecha_pago">Fecha</option>
                    <option value="monto">Monto</option>
                    <option value="id">ID</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('id')">
                            <div class="flex items-center gap-1">
                                ID
                                @if($sortField === 'id')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Venta
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Método
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('monto')">
                            <div class="flex items-center justify-center gap-1">
                                Monto
                                @if($sortField === 'monto')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('fecha_pago')">
                            <div class="flex items-center justify-center gap-1">
                                Fecha
                                @if($sortField === 'fecha_pago')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pagos as $pago)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out table-row">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">#{{ $pago->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Venta #{{ $pago->venta_id }}</div>
                                <div class="text-sm text-gray-500">{{ $pago->user->name ?? 'Sistema' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-{{ $pago->metodoPago->tipo === 'efectivo' ? 'money-bill' : 'credit-card' }} mr-1"></i>
                                    {{ $pago->metodoPago->nombre }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-bold text-gray-900">
                                        ${{ number_format($pago->monto, 2) }}
                                    </span>
                                    @if($pago->comision > 0)
                                        <span class="text-xs text-red-500">
                                            -${{ number_format($pago->comision, 2) }} comisión
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $estadoConfig = match($pago->estado) {
                                        'completado' => ['color' => 'green', 'icon' => 'check-circle'],
                                        'pendiente' => ['color' => 'yellow', 'icon' => 'clock'],
                                        'fallido' => ['color' => 'red', 'icon' => 'times-circle'],
                                        'reversado' => ['color' => 'gray', 'icon' => 'undo'],
                                        default => ['color' => 'gray', 'icon' => 'question-circle']
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $estadoConfig['color'] }}-100 text-{{ $estadoConfig['color'] }}-800">
                                    <i class="fas fa-{{ $estadoConfig['icon'] }} mr-1"></i>
                                    {{ ucfirst($pago->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $pago->fecha_pago->format('H:i') }}</span>
                                    <span class="text-xs text-gray-500">{{ $pago->fecha_pago->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center gap-2">
                                    <button 
                                        wire:click="verDetalle({{ $pago->id }})"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-150"
                                    >
                                        <i class="fas fa-eye mr-1"></i>
                                        Ver
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-credit-card text-5xl mb-4"></i>
                                    <p class="text-lg font-medium">No se encontraron pagos</p>
                                    <p class="text-sm mt-1">Ajusta los filtros para ver los resultados</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="items-center justify-between mt-6">
        <div class="bg-white px-4 py-3 rounded-lg shadow-sm border">
            {{ $pagos->links() }}
        </div>
    </div>
    
    <style>
    .table-row:hover {
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
    
    /* Mejoras para la paginación de Livewire */
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
