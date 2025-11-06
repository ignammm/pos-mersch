@if ($mostrarModalPago)

    <h4 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-cash-register mr-2 text-blue-500"></i>
            Registrar pago
    </h4>
    <form wire:submit.prevent="confirmarPago">
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
                    max="{{ $clienteDetalle->total_adeudado }}"
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

                <button 
                    type="submit"
                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold"
                    >
                    <i class="fas fa-check mr-2"></i>
                    Confirmar Pago
                </button>
            
        </div>
    </form>                    
@endif