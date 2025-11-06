<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl text-white shadow-lg">
                <i class="fas fa-cash-register text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Abrir Caja</h1>
                <p class="text-gray-500 text-sm mt-1">Ingrese el monto inicial para comenzar las operaciones del día</p>
            </div>
        </div>   
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 md:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Monto Inicial -->
            <div class="lg:col-span-5">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-money-bill-wave mr-2 text-green-500"></i>
                    Monto Inicial *
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-dollar-sign text-gray-400"></i>
                    </div>
                    <input 
                        type="number" 
                        step="0.01"
                        min="0"
                        wire:model="montoInicial" 
                        placeholder="Ej: 1000.00"
                        class="w-full border border-gray-300 rounded-xl pl-10 pr-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-gray-50"
                    >
                </div>
                @error('montoInicial')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Observaciones -->
            <div class="lg:col-span-5">
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
                        placeholder="Observaciones opcionales..."
                        rows="1"
                        class="w-full border border-gray-300 rounded-xl pl-10 pr-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50 resize-none"
                    ></textarea>
                </div>
            </div>

            <!-- Botón -->
            <div class="lg:col-span-2 flex items-end">
                <button 
                    wire:click="abrirCaja" 
                    wire:loading.attr="disabled"
                    class="w-full bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                >
                    <span wire:loading.remove wire:target="abrirCaja">
                        <i class="fas fa-play-circle mr-2"></i>Abrir Caja
                    </span>
                    <span wire:loading wire:target="abrirCaja">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Abriendo...
                    </span>
                </button>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                <div>
                    <p class="text-sm text-blue-800 font-medium">Recomendaciones</p>
                    <p class="text-xs text-blue-600 mt-1">
                        • Verifique contar con suficiente cambio antes de abrir la caja<br>
                        • El monto inicial debe ser exacto para facilitar el cierre del día<br>
                        • Registre cualquier observación relevante para el control
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado actual de caja (si hay alguna abierta) -->
    @if($cajaAbierta)
    <div class="mt-6 p-4 bg-yellow-50 rounded-xl border border-yellow-200">
        <div class="flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-yellow-500"></i>
            <div>
                <p class="text-sm text-yellow-800 font-medium">¡Atención!</p>
                <p class="text-xs text-yellow-600 mt-1">
                    Ya existe una caja abierta. Debe cerrar la caja actual antes de abrir una nueva.
                </p>
            </div>
        </div>
    </div>
    @endif
</div>