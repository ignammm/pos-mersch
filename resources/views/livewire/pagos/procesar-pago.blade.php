<!-- resources/views/livewire/pagos/procesar-pago.blade.php -->
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <div class="p-2 bg-green-500 rounded-lg text-white">
            <i class="fas fa-plus text-xl"></i>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Procesar Pago</h1>
            <p class="text-gray-600 text-sm mt-1">Registrar un nuevo pago en el sistema</p>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        @if(session()->has('message'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center gap-2 text-green-800">
                    <i class="fas fa-check-circle"></i>
                    <span class="font-medium">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center gap-2 text-red-800">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <form wire:submit.prevent="procesarPago" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Factura ID -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-receipt mr-2 text-gray-400"></i>
                        Factura ID
                    </label>
                    <input 
                        type="number" 
                        wire:model="facturaId" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                        {{ $facturaId ? 'readonly' : '' }}
                    >
                    @error('facturaId') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Método de Pago -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-credit-card mr-2 text-gray-400"></i>
                        Método de Pago
                    </label>
                    <select wire:model="metodoPagoId" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                        <option value="">Seleccionar método</option>
                        @foreach($metodosPago as $metodo)
                            <option value="{{ $metodo->id }}">
                                {{ $metodo->nombre }} - {{ $metodo->comision_porcentaje }}%
                            </option>
                        @endforeach
                    </select>
                    @error('metodoPagoId') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Monto -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave mr-2 text-gray-400"></i>
                        Monto
                    </label>
                    <input 
                        type="number" 
                        step="0.01" 
                        wire:model="monto" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                    >
                    @error('monto') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Referencia -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-hashtag mr-2 text-gray-400"></i>
                        Referencia
                    </label>
                    <input 
                        type="text" 
                        wire:model="referencia" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                        placeholder="Número de transacción, voucher, etc."
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Código de Autorización -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2 text-gray-400"></i>
                        Código de Autorización
                    </label>
                    <input 
                        type="text" 
                        wire:model="codigoAutorizacion" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                    >
                </div>
            </div>

            <!-- Botón de envío -->
            <div class="flex justify-end pt-4">
                <button 
                    type="submit" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                >
                    <i class="fas fa-credit-card mr-2"></i>
                    Procesar Pago
                </button>
            </div>
        </form>
    </div>
</div>