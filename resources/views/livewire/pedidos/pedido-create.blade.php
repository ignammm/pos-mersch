<div class="space-y-6">
    <!-- Header mejorado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-orange-500 rounded-lg text-white">
                <i class="fas fa-boxes text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Gestión de Reposición</h1>
                <p class="text-gray-600 text-sm mt-1">Selecciona los artículos para reponer en inventario</p>
            </div>
        </div>
        <div class="relative w-full md:w-auto">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input 
                    type="text" 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 w-full transition-all duration-200" 
                    placeholder="Buscar artículos..."
                    wire:model="search"
                >
        </div>
          
    </div>


    <!-- Controles de filtro y búsqueda -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            
           
        </div>
        
    </div>

    <!-- Tabla mejorada -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Artículo</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unitario</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($detalles_venta as $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out {{ $item->repuesto == 1 ? 'bg-green-50' : '' }}">
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                    {{ $item->cantidad }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->articulo->articulo }}</div>
                                <div class="text-sm text-gray-500">SKU: {{ $item->articulo->id ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $item->articulo->marca }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($item->articulo->precio, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                ${{ number_format($item->cantidad * $item->articulo->precio, 2) }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <input 
                                    type="checkbox" 
                                    wire:model='seleccionados'
                                    value="{{ $item->id }}" 
                                    {{ $item->repuesto == 1 ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                                >
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-box-open text-5xl mb-4"></i>
                                    <p class="text-lg font-medium">No se encontraron artículos</p>
                                    <p class="text-sm mt-1">Los artículos vendidos aparecerán aquí para reposición</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación y acciones -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6 w-full">
        <div class="w-full md:flex-1">
            {{ $detalles_venta->links() }}
        </div>
        <button 
            wire:click="guardar" 
            class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg w-full md:w-auto"
        >
            
            Guardar Pedido

        </button>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function actualizarContadores() {
            const checkboxes = document.querySelectorAll('.checkbox-item:checked');
            const totalSeleccionados = checkboxes.length;
            const contadorSeleccionados = document.getElementById('contador-seleccionados');
            const guardarBtn = document.getElementById('guardar-btn');
            const badgeCount = document.getElementById('badge-count');
            
            // Actualizar contador
            contadorSeleccionados.textContent = totalSeleccionados;
            
            // Actualizar badge en botón
            if (totalSeleccionados > 0) {
                badgeCount.textContent = totalSeleccionados;
                badgeCount.classList.remove('hidden');
                guardarBtn.disabled = false;
                guardarBtn.classList.remove('opacity-50');
            } else {
                badgeCount.classList.add('hidden');
                guardarBtn.disabled = true;
                guardarBtn.classList.add('opacity-50');
            }
            
            // Actualizar otros contadores (simulación)
            document.getElementById('contador-por-reponer').textContent = 
                document.querySelectorAll('.checkbox-item:not(:checked)').length;
            document.getElementById('contador-repuestos').textContent = totalSeleccionados;
            
            // Calcular valor total (simulación)
            let valorTotal = 0;
            checkboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const subtotal = row.querySelector('td:nth-child(6)').textContent.replace('$', '');
                valorTotal += parseFloat(subtotal);
            });
            document.getElementById('valor-total').textContent = '$' + valorTotal.toFixed(2);
        }
        
        // Select all functionality
        document.getElementById('select-all').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('.checkbox-item');
            checkboxes.forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
            actualizarContadores();
        });
        
        // Actualizar contadores cuando cambien los checkboxes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('checkbox-item')) {
                actualizarContadores();
            }
        });
        
        // Inicializar contadores
        actualizarContadores();
    });
    </script>
    
    <style>
    .table-row:hover {
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
    
    .checkbox-item:checked {
        background-color: #f97316;
        border-color: #f97316;
    }
    
    #guardar-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    #guardar-btn:not(:disabled):hover {
        transform: translateY(-2px);
    }
    </style>
</div>
