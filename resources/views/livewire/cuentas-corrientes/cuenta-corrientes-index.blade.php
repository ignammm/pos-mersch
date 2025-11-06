<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Cuentas Corrientes
                    </h1>
                    <p class="text-gray-600 mt-2">Gestión de créditos y saldos de clientes</p>
                </div>
                
                <div class="flex gap-3">
                    <button wire:click="exportarReporte"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl font-semibold transition-colors flex items-center gap-2">
                        <i class="fas fa-file-export"></i>
                        Exportar Reporte
                    </button>
                </div>
            </div>
        </div>

        <!-- Panel de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Clientes con CC</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $clientes->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Clientes al día</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $clientes->where('saldo_actual', '<=', 0)->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pendientes</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $clientes->where('saldo_actual', '>', 0)->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Adeudado</p>
                        <p class="text-2xl font-bold text-red-600">
                            ${{ number_format($clientes->sum('total_adeudado'), 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Búsqueda y Filtros -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               wire:model.live="search"
                               placeholder="Buscar cliente por nombre, CUIT o DNI..."
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Clientes -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 cursor-pointer"
                                wire:click="sortBy('nombre')">
                                <div class="flex items-center gap-2">
                                    <span>Cliente</span>
                                    @if($sortField === 'nombre')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-400"></i>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Contacto</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Saldo Vencido</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Saldo Actual</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Estado</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($clientes as $cliente)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $cliente->nombre }}</div>
                                            <div class="text-sm text-gray-500">{{ $cliente->cuit ?? $cliente->dni }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $cliente->telefono ?? '—' }}</div>
                                    <div class="text-sm text-gray-500">{{ $cliente->email ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-red-700">
                                        ${{ number_format($cliente->limite_credito, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold {{ $cliente->saldo_actual > 0 ? 'text-gray-900' : 'text-green-600' }}">
                                        ${{ number_format($cliente->saldo_actual, 2) }}
                                    </div>
                                    @if($cliente->saldo_actual > 0)
                                        <div class="text-xs text-gray-500">
                                            {{ $cliente->movimientos_pendientes }} mov. pendientes
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $estadoConfig = [
                                            'clases' => $cliente->saldo_actual > 0 ? 
                                                'bg-yellow-100 text-yellow-800' : 
                                                'bg-green-100 text-green-800',
                                            'texto' => $cliente->saldo_actual > 0 ? 
                                                    'Deudor' : 
                                                'Al día',
                                            'icono' => $cliente->saldo_actual > 0 ? 
                                                    'clock' : 
                                                'check-circle'
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full {{ $estadoConfig['clases'] }}">
                                        <i class="fas fa-{{ $estadoConfig['icono'] }} mr-1 text-xs"></i>
                                        {{ $estadoConfig['texto'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button wire:click="verDetalles({{ $cliente->id }})"
                                                class="text-blue-600 hover:text-blue-900 transition-colors p-2 rounded-lg hover:bg-blue-50"
                                                title="Ver detalles y movimientos">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        @if($cliente->saldo_actual > 0)
                                        <button wire:click="verDetalles({{ $cliente->id }})"
                                                class="text-green-600 hover:text-green-900 transition-colors p-2 rounded-lg hover:bg-green-50"
                                                title="Registrar pago">
                                            <i class="fas fa-credit-card"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                                        <p class="text-lg font-medium mb-2">No se encontraron clientes</p>
                                        <p class="text-sm">No hay clientes con cuenta corriente activa</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($clientes->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $clientes->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Detalles y Pago -->
    @if($showModal && $clienteDetalle)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl max-w-4xl w-full max-h-[90vh] flex flex-col">
            <!-- Header Fijo -->
            <div class="px-6 py-4 border-b flex-shrink-0 bg-white sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-user mr-2 text-blue-500"></i>
                        {{ $clienteDetalle->nombre }}
                    </h3>
                    <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-1">
                    CUIT/DNI: {{ $clienteDetalle->cuit ?? $clienteDetalle->dni }} 
                </p>
            </div>
            
            <!-- Contenido Scrollable -->
            <div class="flex-1 overflow-y-auto">
                <div class="p-6">
                    <!-- Resumen -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm text-gray-600">Saldo Actual</p>
                            <p class="text-2xl font-bold {{ $clienteDetalle->saldo_actual > 0 ? 'text-red-600' : 'text-green-600' }}">
                                ${{ number_format($clienteDetalle->saldo_actual, 2) }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm text-gray-600">Movimientos Pendientes</p>
                            <p class="text-2xl font-bold text-orange-600">
                                {{ $clienteDetalle->movimientos_pendientes }}
                            </p>
                        </div>
                        
                    </div>

                    <!-- Facturas Pendientes -->
                    <div class="border-t pt-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-file-invoice mr-2 text-blue-500"></i>
                            Facturas Pendientes
                        </h4>

                        @if(count($facturasPendientes) > 0)
                            <div class="space-y-3 mb-6">
                                @foreach($facturasPendientes as $factura)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex items-center gap-4 flex-1">
                                            <div class="flex-1">
                                                <label for="factura-{{ $factura['id'] }}" class="cursor-pointer">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-file-invoice text-blue-600"></i>
                                                        </div>
                                                        <div>
                                                            <p class="font-medium text-gray-900">Factura #{{ $factura['numero'] }}</p>
                                                            <p class="text-sm text-gray-500">
                                                                {{ \Carbon\Carbon::parse($factura['fecha'])->format('d/m/Y') }} | 
                                                                Total: ${{ number_format($factura['total'], 2) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                    <div class="text-right">
                                            <p class="font-semibold text-red-600">
                                                ${{ number_format($factura['saldo_pendiente'], 2) }}
                                            </p>
                                            
                                            @php
                                                $fechaFactura = \Carbon\Carbon::parse($factura['fecha']);
                                                $fechaVencimiento = $fechaFactura->addDays(30);
                                                $estaVencido = $fechaVencimiento->isPast();
                                                $diasCompletos = now()->diffInDays($fechaVencimiento);
                                            @endphp

                                            @if($estaVencido)
                                                <p class="text-xs font-semibold text-red-600 bg-red-100 px-2 py-1 rounded-full inline-block">
                                                    ⚠️ Vencido {{ $fechaVencimiento->diffForHumans() }}
                                                </p>
                                            @else
                                                @if($diasCompletos <= 3)
                                                    <p class="text-xs font-semibold text-red-600 bg-red-100 px-2 py-1 rounded-full inline-block">
                                                        ⏳ Vence {{ $fechaVencimiento->diffForHumans() }}
                                                    </p>
                                                @elseif($diasCompletos <= 7)
                                                    <p class="text-xs font-semibold text-orange-600 bg-orange-100 px-2 py-1 rounded-full inline-block">
                                                        ⏳ Vence {{ $fechaVencimiento->diffForHumans() }}
                                                    </p>
                                                @elseif($diasCompletos <= 15)
                                                    <p class="text-xs font-semibold text-yellow-600 bg-yellow-100 px-2 py-1 rounded-full inline-block">
                                                        📅 Vence {{ $fechaVencimiento->diffForHumans() }}
                                                    </p>
                                                @else
                                                    <p class="text-xs text-gray-500 inline-block">
                                                        ✅ Vence {{ $fechaVencimiento->diffForHumans() }}
                                                    </p>
                                                @endif
                                            @endif
                                            
                                            <p class="text-sm text-gray-500 mt-1">Saldo pendiente</p>
                                            
                                            @if(in_array($factura['id'], $facturasSeleccionadas) && $factura['monto_aplicar'] > 0)
                                                <p class="text-sm font-semibold text-green-600 mt-1">
                                                    💰 Aplicar: ${{ number_format($factura['monto_aplicar'], 2) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 bg-gray-50 rounded-lg">
                                <i class="fas fa-check-circle text-green-400 text-3xl mb-2"></i>
                                <p class="text-gray-600">El cliente no tiene facturas pendientes</p>
                            </div>
                        @endif
                    </div>

                    <!-- Formulario de pago -->
                    @include('cuenta_corrientes.partials.modal-pago')

                    <!-- Historial de Movimientos -->
                    <div class="border-t pt-6 mt-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-history mr-2 text-blue-500"></i>
                            Últimos Movimientos
                        </h4>
                        
                        @if($clienteDetalle->cuentasCorrientes->count() > 0)
                            <div class="space-y-3">
                                @foreach($clienteDetalle->cuentasCorrientes->take(10) as $movimiento)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $movimiento->descripcion }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold {{ $movimiento->tipo_movimiento === 'debe' ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $movimiento->tipo_movimiento === 'debe' ? '-' : '+' }}${{ number_format($movimiento->monto, 2) }}
                                            </p>
                                            <span class="text-xs px-2 py-1 rounded-full {{ $movimiento->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($movimiento->estado) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No hay movimientos registrados</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endif
</div>