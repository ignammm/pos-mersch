@if($mostrarPago && $facturaSeleccionada)
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto">
    <!-- Contenedor principal con altura máxima y scroll -->
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full max-h-[90vh] flex flex-col">
        <!-- Header fijo -->
        <div class="px-6 py-4 border-b flex-shrink-0">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-credit-card mr-2 text-green-500"></i>
                    Registrar Pago
                </h3>
                <button wire:click="cerrarModalPago" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-sm text-gray-600 mt-1">
                Cliente: {{ $facturaSeleccionada['cliente']->nombre ?? 'Consumidor Final' }}
            </p>
        </div>

        <!-- Contenido con scroll -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-6">
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Total Factura:</p>
                            <p class="font-semibold">${{ number_format($facturaSeleccionada['monto_original'], 2) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Saldo Pendiente:</p>
                            <p class="font-semibold text-red-600">${{ number_format($facturaSeleccionada['saldo_pendiente'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="confirmarVentaConPago">
                    <div class="space-y-4">
                        <!-- Monto del pago -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Monto a Pagar *
                            </label>
                            <input 
                                type="number" 
                                step="0.01"
                                wire:model="montoPago" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"
                                placeholder="0.00"
                                max="{{ $facturaSeleccionada['saldo_pendiente'] }}"
                            >
                            @error('montoPago')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Método de pago -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Método de Pago *
                            </label>
                            <select wire:model.live="metodoPago" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                                <option value="1">Efectivo</option>
                                <option value="4">Transferencia</option>
                                <option value="3">Tarjeta Débito</option>
                                <option value="2">Tarjeta Crédito</option>
                                <option value="5">Cuenta Corriente</option>
                                <option value="6">Cheque</option>
                                <option value="7">Otros</option>
                            </select>
                        </div>

                        <!-- TRANSFERENCIA (ID: 4) -->
                        @if($metodoPago == 4)
                        <div class="space-y-4 border-t pt-4">
                            <h4 class="font-semibold text-gray-700">
                                <i class="fas fa-university mr-2 text-blue-500"></i>
                                Datos de Transferencia
                            </h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Entidad Bancaria *
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="entidadBancaria" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                                    placeholder="Ej: Banco Nación, Santander, etc."
                                    required
                                >
                                @error('entidadBancaria')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <!-- TARJETA CRÉDITO/DÉBITO (ID: 2, 3) -->
                        @if(in_array($metodoPago, [2, 3]))
                        <div class="space-y-4 border-t pt-4">
                            <h4 class="font-semibold text-gray-700">
                                <i class="fas fa-credit-card mr-2 text-purple-500"></i>
                                Datos de Tarjeta
                            </h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Cuotas
                                    </label>
                                    <select wire:model="datosTarjeta.cuotas" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}">{{ $i }} cuota{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Opcional: últimos 4 dígitos de la tarjeta -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Últimos 4 dígitos (opcional)
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="datosTarjeta.numero" 
                                    maxlength="4"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500"
                                    placeholder="1234"
                                >
                            </div>
                        </div>
                        @endif

                        <!-- CHEQUE (ID: 6) -->
                        @if($metodoPago == 6)
                        <div class="space-y-4 border-t pt-4">
                            <h4 class="font-semibold text-gray-700">
                                <i class="fas fa-money-check mr-2 text-orange-500"></i>
                                Datos del Cheque
                            </h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Número de Cheque *
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="numeroCheque" 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500"
                                        placeholder="Ej: 123456"
                                        required
                                    >
                                    @error('numeroCheque')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Fecha Vencimiento *
                                    </label>
                                    <input 
                                        type="date" 
                                        wire:model="fechaVencimientoCheque" 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500"
                                        min="{{ date('Y-m-d') }}"
                                        required
                                    >
                                    @error('fechaVencimientoCheque')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Entidad Bancaria *
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="entidadBancariaCheque" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500"
                                    placeholder="Ej: Banco Galicia, BBVA, etc."
                                    required
                                >
                                @error('entidadBancariaCheque')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <!-- CUENTA CORRIENTE (ID: 5) - Solo información -->
                        @if($metodoPago == 5)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                <span class="font-semibold">Venta a Cuenta Corriente</span>
                            </div>
                            <p class="text-sm text-yellow-700 mt-1">
                                Se registrará como crédito para el cliente. El pago quedará pendiente.
                            </p>
                        </div>
                        @endif

                        <!-- Referencia -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Referencia
                            </label>
                            <input 
                                type="text" 
                                wire:model="referenciaPago" 
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"
                                placeholder="Nº operación, último 4 dígitos, etc."
                            >
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Observaciones
                            </label>
                            <textarea 
                                wire:model="observacionesPago" 
                                rows="2"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"
                                placeholder="Observaciones adicionales..."
                            ></textarea>
                        </div>
                    </div>

                    <!-- Botones fijos en la parte inferior -->
                    <div class="flex gap-3 mt-6 pt-4 border-t">
                        <button 
                            type="button" 
                            wire:click="cerrarModalPago"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold"
                        >
                            <i class="fas fa-check mr-2"></i>
                            Confirmar Pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif