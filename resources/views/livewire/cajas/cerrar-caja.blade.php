<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-gradient-to-br from-red-500 to-red-600 rounded-xl text-white shadow-lg">
                <i class="fas fa-cash-register text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Cerrar Caja</h1>
                <p class="text-gray-500 text-sm mt-1">Complete los datos para realizar el cierre diario de caja</p>
            </div>
        </div>   
    </div>

    <!-- Mensajes de éxito/error -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <p class="text-red-800 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($cajaAbierta && $resumenCaja)
    <!-- Resumen de la caja actual -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Resumen de Caja Actual</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                <p class="text-sm text-blue-600 font-medium">Monto Inicial</p>
                <p class="text-2xl font-bold text-blue-800">${{ number_format($cajaAbierta->monto_inicial, 2) }}</p>
            </div>
            
            <div class="bg-green-50 p-4 rounded-xl border border-green-200">
                <p class="text-sm text-green-600 font-medium">Total Ingresos</p>
                <p class="text-2xl font-bold text-green-800">${{ number_format($resumenCaja['total_ingresos'], 2) }}</p>
            </div>
            
            <div class="bg-red-50 p-4 rounded-xl border border-red-200">
                <p class="text-sm text-red-600 font-medium">Total Egresos</p>
                <p class="text-2xl font-bold text-red-800">${{ number_format($resumenCaja['total_egresos'], 2) }}</p>
            </div>
            
            <div class="bg-purple-50 p-4 rounded-xl border border-purple-200">
                <p class="text-sm text-purple-600 font-medium">Saldo Esperado</p>
                <p class="text-2xl font-bold text-purple-800">${{ number_format($resumenCaja['saldo_actual'], 2) }}</p>
            </div>
        </div>

        <!-- Ingresos por método de pago -->
        @if(count($resumenCaja['ingresos_por_metodo']) > 0)
        <div class="mb-6">
            <h4 class="text-md font-semibold text-gray-700 mb-3">Ingresos por Método de Pago</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($resumenCaja['ingresos_por_metodo'] as $metodoId => $monto)
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-600">
                        {{ $metodoId ? \App\Models\MetodoPago::find($metodoId)->nombre : 'Varios' }}
                    </p>
                    <p class="font-semibold text-gray-800">${{ number_format($monto, 2) }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Información de la caja -->
        <div class="bg-gray-50 p-4 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-600"><strong>Abierta por:</strong> {{ $cajaAbierta->user->name }}</p>
                    <p class="text-gray-600"><strong>Fecha apertura:</strong> {{ $cajaAbierta->fecha_apertura->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-600"><strong>Duración:</strong> {{ $cajaAbierta->fecha_apertura->diffForHumans(now(), true) }}</p>
                    <p class="text-gray-600"><strong>Total ventas:</strong> {{ $resumenCaja['total_ingresos'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de cierre -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 md:p-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Datos del Cierre</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Monto Final Real -->
            <div class="lg:col-span-5">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-money-bill-wave mr-2 text-red-500"></i>
                    Monto Final Real *
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-dollar-sign text-gray-400"></i>
                    </div>
                    <input 
                        type="number" 
                        step="0.01"
                        min="0"
                        wire:model.live="montoFinalReal" 
                        placeholder="Ingrese el monto contado físicamente..."
                        class="w-full border border-gray-300 rounded-xl pl-10 pr-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 bg-gray-50"
                    >
                </div>
                @error('montoFinalReal')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Diferencia Calculada -->
            <div class="lg:col-span-3">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-calculator mr-2 text-orange-500"></i>
                    Diferencia
                </label>
                <div class="p-3 bg-gray-50 border border-gray-300 rounded-xl">
                    @php
                        $diferencia = $this->calcularDiferencia();
                        $claseDiferencia = $diferencia == 0 ? 'text-green-600' : ($diferencia > 0 ? 'text-orange-600' : 'text-red-600');
                    @endphp
                    <p class="font-semibold {{ $claseDiferencia }} text-lg">
                        ${{ number_format(abs($diferencia), 2) }}
                        @if($diferencia > 0)
                            <span class="text-sm">(Sobrante)</span>
                        @elseif($diferencia < 0)
                            <span class="text-sm">(Faltante)</span>
                        @else
                            <span class="text-sm">(Exacto)</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="lg:col-span-4">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-sticky-note mr-2 text-blue-500"></i>
                    Observaciones
                </label>
                <div class="relative">
                    <div class="absolute top-3 left-3 pointer-events-none">
                        <i class="fas fa-comment text-gray-400"></i>
                    </div>
                    <textarea 
                        wire:model="observaciones" 
                        placeholder="Observaciones del cierre..."
                        rows="1"
                        class="w-full border border-gray-300 rounded-xl pl-10 pr-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 resize-none"
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- Botón de cierre -->
        <div class="mt-8 flex justify-end">
            <button 
                wire:click="cerrarCaja" 
                wire:loading.attr="disabled"
                class="bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            >
                <span wire:loading.remove wire:target="cerrarCaja">
                    <i class="fas fa-lock mr-2"></i>Cerrar Caja
                </span>
                <span wire:loading wire:target="cerrarCaja">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Cerrando...
                </span>
            </button>
        </div>

        <!-- Información adicional -->
        <div class="mt-6 p-4 bg-yellow-50 rounded-xl border border-yellow-200">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-yellow-500 mt-1"></i>
                <div>
                    <p class="text-sm text-yellow-800 font-medium">Antes de cerrar la caja</p>
                    <p class="text-xs text-yellow-600 mt-1">
                        • Verifique haber contado todo el efectivo físicamente<br>
                        • Confirme que todos los movimientos estén registrados<br>
                        • Revise que no haya ventas pendientes de pago<br>
                        • Asegúrese de tener el monto exacto para el próximo día
                    </p>
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- Mensaje cuando no hay caja abierta -->
    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-8 text-center">
        <div class="max-w-md mx-auto">
            <div class="p-4 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-cash-register text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay caja abierta</h3>
            <p class="text-gray-500 mb-4">Actualmente no existe ninguna caja abierta que pueda ser cerrada.</p>
            <a href="{{ route('cajas.abrir') }}" 
               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">
                <i class="fas fa-plus"></i>
                Abrir Nueva Caja
            </a>
        </div>
    </div>
    @endif
</div>