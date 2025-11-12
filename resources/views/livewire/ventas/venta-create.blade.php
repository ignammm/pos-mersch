@php
    $clientesArray = [];
    foreach ($clientes as $cliente) {
        $clientesArray[$cliente->id] = $cliente->nombre;
    }
@endphp

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50/30 p-6">
    <!-- Header Mejorado -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <button onclick="window.location.href='{{ route('ventas.index') }}'"
                class="group flex items-center gap-2 px-4 py-3 bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-blue-200 transition-all duration-200">
                <svg class="w-5 h-5 text-gray-600 group-hover:text-blue-600 transition-colors"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 5H1m0 0 4 4M1 5l4-4" />
                </svg>
                <span class="text-gray-700 group-hover:text-blue-600 font-medium">Volver</span>
            </button>

            <div class="flex-1">
                <h1
                    class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                    Nueva Venta
                </h1>
                <p class="text-gray-600">Complete los datos de la venta</p>
            </div>
        </div>
    </div>

    <!-- Notificación de éxito -->
    <div x-data="{ mostrar: false }" x-init="@this.on('venta-create', () => {
        mostrar = true;
        setTimeout(() => mostrar = false, 3000);
    })" class="fixed bottom-6 right-6 z-50">
        <div x-show="mostrar" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 border-l-4 border-green-400">
            <div class="flex items-center justify-center w-6 h-6 bg-green-400 rounded-full">
                <i class="fas fa-check text-xs"></i>
            </div>
            <span class="font-semibold">¡Venta registrada correctamente!</span>
        </div>
    </div>

    <!-- Mensajes de validación mejorados -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-center gap-2 text-red-800 mb-2">
                <i class="fas fa-exclamation-triangle"></i>
                <span class="font-semibold">Por favor corrige los siguientes errores:</span>
            </div>
            <ul class="text-red-700 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="flex items-center gap-2">
                        <i class="fas fa-circle text-[8px]"></i>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Panel de datos del cliente -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-user text-blue-500"></i>
            Datos del Cliente
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Cliente</label>
                <div class="relative">
                    <select wire:model.live="cliente_id"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none bg-white">
                        <option value="13" class="text-gray-400">Selecciona un cliente...</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}" class="text-gray-700">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <i
                        class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                </div>
            </div>

            @if ($cliente_id && $cliente_id != 13)
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                    <div class="flex items-center gap-2 text-blue-800 mb-2">
                        <i class="fas fa-info-circle"></i>
                        <span class="font-semibold">Cliente seleccionado</span>
                    </div>
                    @php $clienteSeleccionado = $clientes->firstWhere('id', $cliente_id) @endphp
                    <p class="text-blue-700">{{ $clienteSeleccionado->nombre ?? '' }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-user text-blue-500"></i>
            Datos del Cliente
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Cliente</label>
                <livewire:searchableselect :options="$clientesArray" wire:model.live="cliente_id" icon="fas fa-user"
                    placeholder="Selecciona un cliente..." />
            </div>

            @if ($cliente_id && $cliente_id != 13)
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                    <div class="flex items-center gap-2 text-blue-800 mb-2">
                        <i class="fas fa-info-circle"></i>
                        <span class="font-semibold">Cliente seleccionado</span>
                    </div>
                    @php $clienteSeleccionado = $clientes->firstWhere('id', $cliente_id) @endphp
                    <p class="text-blue-700">{{ $clienteSeleccionado->nombre ?? '' }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Panel para agregar artículos -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-cube text-green-500"></i>
            Agregar Artículos
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Código de barras -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Código de Barras</label>
                <div class="relative">
                    <input wire:model="codigo_barra" wire:keydown.enter="agregarArticulo" type="text"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                        placeholder="Escanear código...">
                    <i class="fas fa-barcode absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Cantidad -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cantidad</label>
                <input wire:model.live="cantidad" type="number"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                    min="1" value="1">
            </div>

            <!-- Descuento -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descuento</label>
                <div class="relative">
                    <input wire:model.live="descuento" type="number"
                        class="w-full pr-12 pl-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                        min="0" max="100" placeholder="0">
                    <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500 font-medium pointer-events-none">%</span>
                </div>
            </div>

            <!-- Botón agregar -->
            <div class="flex items-end">
                <button wire:click="agregarArticulo"
                    class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i>
                    Agregar
                </button>
            </div>

            <!-- Botón buscar -->
            <div class="flex items-end">
                <button wire:click="$set('modalSeleccionarArticulo', true)"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i>
                    Buscar
                </button>
            </div>
        </div>
    </div>

    <!-- Panel para agregar artículos -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-cube text-green-500"></i>
            Agregar Artículos
        </h2>
        <div class="flex flex-row gap-2 w-full">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Código de barras -->
                <div>
                    <x-main-input name="codigo_barra" :displayError="false" label="Código de Barras" placeholder="Escanear código..."
                        icon="fas fa-barcode" iconPosition="left" wire:model="codigo_barra"
                        wire:keydown.enter="agregarArticulo"></x-main-input>
                </div>

                <!-- Cantidad -->
                <div>
                    <x-main-input name="cantidad" :displayError="false" type="number" min="1" label="Cantidad"
                        placeholder="Cantidad" wire:model="cantidad"
                        wire:keydown.enter="agregarArticulo"></x-main-input>
                </div>

                <!-- Descuento -->
                <div>
                    <div class="relative">
                        <x-main-input name="descuento" :displayError="false" class="!pe-10" type="number" label="Descuento"
                            placeholder="Descuento" wire:model="descuento"
                            wire:keydown.enter="agregarArticulo"></x-main-input>
                        <span
                            class="absolute top-10 right-4 flex items-center text-gray-500 font-medium pointer-events-none">%</span>
                    </div>
                </div>
                <div class="flex items-end ">
                    <x-main-button label="Agregar" :isLoading="true" class="w-full" icon="fas fa-plus"
                        variant="primary" wire:click="agregarArticulo"
                        wire:keydown.enter="agregarArticulo"></x-main-button>
                </div>

                <!-- Botón buscar -->
                <div class="flex items-end">
                    <x-main-button label="Buscar" :isLoading="true" class="w-full" icon="fas fa-search"
                        variant="info" wire:click="$set('modalSeleccionarArticulo', true)"
                        wire:keydown.enter="agregarArticulo"></x-main-button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de artículos agregados -->
    @if (count($items) > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-purple-500"></i>
                    Artículos en la Venta
                    <span class="bg-purple-100 text-purple-800 text-sm font-medium px-2 py-1 rounded-full ml-2">
                        {{ count($items) }} items
                    </span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Artículo</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Rubro</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Marca</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Cantidad</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Precio Unit.</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Desc.</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Subtotal</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($items as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-cube text-blue-600 text-sm"></i>
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $item['nombre'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $item['rubro'] }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $item['marca'] }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="decrementarCantidad({{ $index }})"
                                            class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors">
                                            <i class="fas fa-minus text-gray-600 text-xs"></i>
                                        </button>
                                        <span
                                            class="w-12 text-center font-semibold text-gray-900">{{ $item['cantidad'] }}</span>
                                        <button wire:click="incrementarCantidad({{ $index }})"
                                            class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors">
                                            <i class="fas fa-plus text-gray-600 text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900">
                                    ${{ number_format($item['precio_unitario'], 0) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $item['descuento_unitario'] > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ number_format($item['descuento_unitario'], 0) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-green-600">
                                    ${{ number_format($item['subtotal'], 0) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <button wire:click="eliminarItem({{ $index }})"
                                        class="text-red-500 hover:text-red-700 transition-colors p-2 rounded-lg hover:bg-red-50">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Resumen y botón finalizar -->
    @if (count($items) > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <div class="text-3xl font-bold text-gray-900 mb-2">${{ number_format($total, 0) }}</div>
                    <p class="text-gray-600 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Total de la venta
                    </p>
                </div>

                <div class="flex gap-3">
                    <button onclick="window.location.href='{{ route('ventas.index') }}'"
                        class="px-8 py-4 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-semibold">
                        Cancelar
                    </button>
                    <button wire:click="finalizarVenta"
                        class="px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md flex items-center gap-2">
                        <i class="fas fa-cash-register"></i>
                        Finalizar Venta
                    </button>
                </div>
            </div>
        </div>
    @else
        <!-- Estado vacío -->
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shopping-cart text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Carrito vacío</h3>
            <p class="text-gray-500 mb-6">Agrega artículos para comenzar una venta</p>
        </div>
    @endif

    <x-modal wire:model.live="modalSeleccionarArticulo">
        <div class="p-6 bg-gray-50 min-h-screen">

            <!-- Título -->
            <h2 class="text-3xl font-extrabold mb-8 text-gray-900 tracking-tight border-b pb-4">
                Seleccione su artículo
            </h2>

            <!-- Lista de artículos -->
            @if ($articulosModal && $articulosModal->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                    @foreach ($articulosModal as $articulo)
                        <div
                            class="bg-white rounded-2xl shadow hover:shadow-xl transition-transform transform hover:-translate-y-1 p-5 flex flex-col justify-between border border-gray-100">

                            <!-- Información -->
                            <div class="space-y-3">
                                <p class="text-gray-900 font-semibold text-lg">
                                    {{ $articulo->articulo ?? '—' }}
                                </p>
                                <p class="text-sm text-blue-500">
                                    <span class="font-medium text-gray-800">Marca:</span>
                                    {{ $articulo->marca ?? '—' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium text-gray-800">Rubro:</span>
                                    {{ $articulo->rubro ?? '—' }}
                                </p>
                                <p class="text-sm text-green-700 font-semibold">
                                    ${{ number_format($articulo->precio ?? 0, 2) }}
                                </p>
                            </div>

                            <!-- Botón seleccionar -->
                            <div class="mt-5 flex justify-end">
                                <button wire:click="confirmarSeleccion({{ $articulo->id }})"
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium shadow-sm">
                                    Seleccionar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center">No se encontraron artículos.</p>
            @endif

            <!-- Botón cancelar -->
            <div class="flex justify-end">
                <button wire:click="$set('modalSeleccionarArticulo', false)"
                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition font-medium shadow-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </x-modal>

    @include('pagos/partials/modal-pagar')

</div>
