<div class="p-4">
    <div class="flex items-center">
        <x-button onclick="window.location.href='{{ route('ventas.index') }}'">
            <svg class="w-4 h-4 text-gray-1200 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
            </svg>
        </x-button>
        <div class="inline-flex items-center px-4 py-2">
            <h1 class="text-4xl font-bold mb-2 ">Nueva Venta</h1>
        </div>
    </div>

    <div 
        x-data="{ mostrar: false }" 
        x-init="@this.on('venta-create', () => { 
            mostrar = true; 
            setTimeout(() => mostrar = false, 2000); 
        })"
        class="fixed bottom-5 right-5 z-50"
    >
        <div 
            x-show="mostrar"
            x-transition
            class="bg-green-600 text-white px-4 py-2 rounded shadow-lg"
        >
            ✅ Venta registrada correctamente
        </div>
    </div>

    {{-- Mensajes de validación --}}
        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul class="text-sm list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


    {{-- DATOS DEL INGRESO --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
            <label class="block text-sm">Cliente</label>
            <select wire:model.live="cliente_id" class="w-full border rounded px-2 py-1">
                <option value="13">Selecciona...</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm">Forma de pago</label>
            <select wire:model.live="forma_pago" class="w-full border rounded px-2 py-1" required>
                <option value="Efectivo">Efectivo</option>
                <option value="Tarjeta">Tarjeta</option>
                <option value="Transferencia">Transferencia</option>
                <option value="Valores">Valores</option>
                <option value="Divisas">Divisas</option>
            </select>
        </div>

        <div>
            <label class="block text-sm">Tipo Comprobante</label>
            <select wire:model.live="tipo_comprobante" class="w-full border rounded px-2 py-1" required>
                <option value="Ticket">Ticket</option>
                <option value="Factura A">Factura A</option>
                <option value="Factura B">Factura B</option>
            </select>
        </div>

    </div>

    <hr class="mb-4">

    {{-- AGREGAR ARTÍCULOS --}}
    <h3 class="text-lg font-semibold mb-2">Agregar artículos</h3>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block text-sm">Codigo</label>
            <input wire:model="codigo_barra"  wire:keydown.enter="agregarArticulo"  type="text" class="w-full border rounded px-2 py-1" required>
        </div>

        <div>
            <label class="block text-sm">Cantidad</label>
            <input wire:model.live="cantidad" type="number" class="w-full border rounded px-2 py-1">
        </div>

        <div>
            <label class="block text-sm">Descuento</label>
            <div class="relative">
                <input
                    wire:model.live="descuento"
                    type="number"
                    class="w-full pr-10 border rounded px-2 py-1"
                >
                <span class="absolute inset-y-0 right-2 flex items-center text-gray-500 text-sm pointer-events-none">%</span>
            </div>
        </div>


        <div class="flex items-end">
            <button wire:click="agregarArticulo" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Agregar
            </button>
        </div>
    </div>

    {{-- LISTA DE DETALLES --}}
    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Articulo</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Rubro</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Marca</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Cantidad</th>
                    <th class="px-4 py-2 text-right text-sm text-gray-700">Precio</th>
                    <th class="px-4 py-2 text-right text-sm text-gray-700">Descuento</th>
                    <th class="px-4 py-2 text-center text-sm text-gray-700">Subtotal</th>
                     <th class="px-4 py-2 text-center text-sm text-gray-700">Acciones</th>
                    
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($items as $index => $item)
                    <tr>
                        
                        <td class="px-4 py-2">{{ $item['nombre'] }}</td>
                        <td class="px-4 py-2">{{ $item['rubro'] }}</td>
                        <td class="px-4 py-2">{{ $item['marca'] }}</td>
                        <td class="px-4 py-2">
                            <div class="flex items-center space-x-2">
                                <button 
                                    wire:click="decrementarCantidad({{ $index }})" 
                                    class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">
                                    -
                                </button>

                                <span>{{ $item['cantidad'] }}</span>

                                <button 
                                    wire:click="incrementarCantidad({{ $index }})" 
                                    class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">
                                    +
                                </button>
                            </div>
                        </td>
                        <td class="px-4 py-2 text-right">${{ number_format($item['precio_unitario']), 0}}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($item['descuento_unitario']), 0}}%</td>
                        <td class="px-4 py-2 text-right">${{ number_format($item['subtotal'], 0) }}</td>
                        <td class="px-4 py-2 text-center">
                            <button wire:click="eliminarItem({{ $index }})" class="text-red-600">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                            No se encontraron artículos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    <div class="text-right mb-4">
        <strong>Total: ${{ number_format($total, 0) }}</strong>
    </div>

    {{-- BOTÓN GUARDAR --}}
    <div class="text-right">
        <x-button wire:click="guardar" class="text-white px-4 py-2 rounded">
            Finalizar Venta
        </x-button>
    </div>

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
                        <div class="bg-white rounded-2xl shadow hover:shadow-xl transition-transform transform hover:-translate-y-1 p-5 flex flex-col justify-between border border-gray-100">
                            
                            <!-- Información -->
                            <div class="space-y-3">
                                <p class="text-gray-900 font-semibold text-lg">
                                    {{ $articulo->articulo ?? '—' }}
                                </p>
                                <p class="text-sm text-blue-500">
                                    <span class="font-medium text-gray-800">Marca:</span> {{ $articulo->marca ?? '—' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium text-gray-800">Rubro:</span> {{ $articulo->rubro ?? '—' }}
                                </p>
                                <p class="text-sm text-green-700 font-semibold">
                                    ${{ number_format($articulo->precio ?? 0, 2) }}
                                </p>
                            </div>

                            <!-- Botón seleccionar -->
                            <div class="mt-5 flex justify-end">
                                <button
                                    wire:click="confirmarSeleccion({{ $articulo->id }})"
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium shadow-sm"
                                >
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
                <button
                    wire:click="$set('modalSeleccionarArticulo', false)"
                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition font-medium shadow-sm"
                >
                    Cancelar
                </button>
            </div>
        </div>
    </x-modal>


</div>
