<div>
    <div class="flex items-center">
        <x-button onclick="window.history.back()">
            <svg class="w-4 h-4 text-gray-1200 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
            </svg>
        </x-button>
        <div class="inline-flex items-center px-4 py-2">
            <h1 class="text-4xl font-bold mb-2 ">Iniciar un Trabajo</h1>
        </div>
    </div>


    <div 
        x-data="{ mostrar: false }" 
        x-init="@this.on('trabajo-create', () => { 
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
            ✅ Trabajo registrado correctamente
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


    {{-- DATOS DEL CLIENTE --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-6">
        <div>
            <label class="block text-sm">Cliente</label>
            <select wire:model.live="cliente_id" class="w-full border rounded px-2 py-1" required>
                <option value="">Seleccionar...</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm">Vehiculo</label>
            <select wire:model.live="patente" class="w-full border rounded px-2 py-1
            @if(!$cliente_id) bg-gray-100 text-gray-500 cursor-not-allowed @endif"
            @disabled(!$cliente_id) required>
                <option value="">Seleccionar...</option>
                @foreach ($vehiculos_cliente as $vehiculo_cliente)
                    <option value="{{ $vehiculo_cliente->id }}">{{ $vehiculo_cliente->patente }}</option>
                @endforeach
            </select>
        </div>
      
        <div class="flex items-center justify-start">
                <button
                    wire:click='agregarVehiculoCliente'
                    class="text-cyan-500 pt-3 hover:underline {{ !$cliente_id ? 'hidden' : '' }}"
                >
                    + Añadir Vehículo
                </button>
        </div>

    </div>

    <hr class="mb-4">

    {{-- AGREGAR TRABAJO --}}
    <h3 class="text-lg font-semibold mb-2">Datos del Trabajo</h3>
       <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-sm">Nombre</label>
            <input wire:model="nombre_trabajo" type="text" class="w-full border rounded px-2 py-1
            @if(!$patente)  bg-gray-100 text-gray-500 cursor-not-allowed  @endif"
            @disabled(!$patente) required>    
        </div>

        <div>
            <label class="block text-sm">Descripcion</label>
            <textarea wire:model="descripcion_trabajo" type="text" class="w-full border rounded px-2 py-1
            @if(!$patente) bg-gray-100 text-gray-500 cursor-not-allowed  @endif"
            @disabled(!$patente)></textarea>
        </div>
    </div>



    <hr class="mb-4">

    {{-- AGREGAR ARTÍCULOS --}}
    <h3 class="text-lg font-semibold mb-2">Agregar artículos</h3>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block text-sm">Codigo</label>
            <input wire:model="codigo_barra"  wire:keydown.enter="agregarArticulo"  type="text" class="w-full border rounded px-2 py-1
            @if(!$patente)  bg-gray-100 text-gray-500 cursor-not-allowed  @endif"
            @disabled(!$patente)>    
        </div>

        <div>
            <label class="block text-sm">Cantidad</label>
            <input wire:model.live="cantidad" type="number" class="w-full border rounded px-2 py-1
            @if(!$patente) bg-gray-100 text-gray-500 cursor-not-allowed  @endif"
            @disabled(!$patente)>
        </div>

        <div class="flex items-end">
            <button wire:click="agregarArticulo" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700
            @if(!$patente) cursor-not-allowed @endif"
            @disabled(!$patente)>
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
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Cantidad</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Marca</th>
                    <th class="px-4 py-2 text-right text-sm text-gray-700">Precio Unitario</th>
                    <th class="px-4 py-2 text-center text-sm text-gray-700">Subtotal</th>
                     <th class="px-4 py-2 text-center text-sm text-gray-700">Acciones</th>
                    
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($items as $index => $item)
                    <tr>
                        
                        <td class="px-4 py-2">{{ $item['nombre'] }}</td>
                        <td class="px-4 py-2">{{ $item['rubro'] }}</td>
                        <td class="px-4 py-2">{{ $item['cantidad'] }}</td>
                         <td class="px-4 py-2">{{ $item['marca'] }}</td>
                        <td class="px-4 py-2 text-right">${{ number_format($item['precio_unitario']), 0}}</td>
                        <td class="px-4 py-2 text-right">${{ number_format($item['subtotal'], 0) }}</td>
                        <td class="px-4 py-2 text-center">
                            <button wire:click="eliminarItem({{ $index }})" class="text-red-600">Eliminar</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                            No se agregaron articulos al trabajo aún.
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
        <x-button wire:click="guardarTrabajo" class="text-white px-4 py-2 rounded">
            Guardar
        </x-button>
    </div>

  
    <x-modal wire:model.live="mostrarModalVehiculo">
        <div class="p-6 bg-gray-50 min-h-60">
            <h2 class="text-3xl font-bold mb-6 text-gray-800">Ingrese los datos del vehiculo:</h2>

            <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-4">
                <div>
                    <label class="block text-sm">Marca</label>
                    <select wire:model.live="marca" class="w-full border rounded px-2 py-1" required>
                        <option value="">Seleccionar...</option>
                        @foreach ($marcas as $marca)
                            <option value="{{ $marca->marca }}">{{ $marca->marca }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm">Modelo</label>
                    <select wire:model.live="modelo" class="w-full border rounded px-2 py-1" required>
                        <option value="">Seleccionar...</option>
                        @foreach ($modelos as $modelo)
                            <option value="{{ $modelo->modelo }}">{{ $modelo->modelo }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm">Año</label>
                    <input wire:model="anio" type="number" class="w-full border rounded px-2 py-1" required>    
                </div>

                <div>
                    <label class="block text-sm">Patente</label>
                    <input wire:model="patente_modal" type="text" class="w-full border rounded px-2 py-1" required>    
                </div>

                <div>
                    <label class="block text-sm">Descripcion</label>
                    <textarea wire:model="descripcion" type="text" class="w-full border rounded px-2 py-1"></textarea>    
                </div>

                <div class="flex items-end">
                    <button wire:click="confirmarVehiculoCliente" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Agregar
                    </button>
                </div>
            </div>

            {{-- Botón cancelar --}}
            <div class="flex justify-end mt-8">
                <button
                    wire:click="$set('mostrarModalVehiculo', false)"
                    class="bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-600 transition"
                >
                    Cancelar
                </button>
            </div>
        </div>
    </x-modal>


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
