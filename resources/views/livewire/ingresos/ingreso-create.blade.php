<div class="p-4">
    <h2 class="text-xl font-bold mb-4">Ingreso de stock</h2>

    <div 
        x-data="{ mostrar: false }" 
        x-init="@this.on('ingreso-create', () => { 
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
            ✅ Ingreso registrado correctamente
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
            <label class="block text-sm">Proveedor</label>
            <select wire:model.live="proveedor_id" class="w-full border rounded px-2 py-1" required>
                <option value="">Seleccionar..</option>
                @foreach ($proveedores as $proveedor)
                    <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm">Tipo de comprobante</label>
            <input wire:model.live="tipo_comprobante" type="text" class="w-full border rounded px-2 py-1
            @if(!$proveedor_id) bg-gray-100 text-gray-500 cursor-not-allowed @endif"
            @disabled(!$proveedor_id)>
        </div>

        <div>
            <label class="block text-sm">Número</label>
            <input wire:model.live="numero_comprobante" type="text" class="w-full border rounded px-2 py-1
            @if(!$proveedor_id) bg-gray-100 text-gray-500 cursor-not-allowed @endif"
            @disabled(!$proveedor_id)>
        </div>
    </div>

    <hr class="mb-4">

    {{-- AGREGAR ARTÍCULOS --}}
    <h3 class="text-lg font-semibold mb-2">Agregar artículos</h3>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block text-sm">Codigo</label>
            <input wire:model="codigo_barra"  wire:keydown.enter="agregarArticulo"  type="text" class="w-full border rounded px-2 py-1
            @if(!$proveedor_id) bg-gray-100 text-gray-500 cursor-not-allowed @endif"
            @disabled(!$proveedor_id) required>
        </div>

        <div>
            <label class="block text-sm">Cantidad</label>
            <input wire:model.live="cantidad" type="number" class="w-full border rounded px-2 py-1
            @if(!$proveedor_id) bg-gray-100 text-gray-500 cursor-not-allowed @endif"
            @disabled(!$proveedor_id)>
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
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Código Proveedor</th>
                    <th class="px-4 py-2 text-left text-sm text-gray-700">Cantidad</th>
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
                        <td class="px-4 py-2">{{ $item['codigo_proveedor'] }}</td>
                        <td class="px-4 py-2">{{ $item['cantidad'] }}</td>
                        <td class="px-4 py-2 text-right">${{ number_format($item['precio_unitario']), 0}}</td>
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
            Guardar ingreso
        </x-button>
    </div>

    <x-modal wire:model.live="mostrarModal">
        <div class="p-4">
            <h2 class="text-lg font-semibold mb-4">Crear nuevo artículo</h2>

            @if ($referenciaSeleccionada)
                <div class="mb-4 space-y-2">
                    <p><strong>Articulo:</strong> {{ $referenciaSeleccionada['articulo'] ?? '' }}</p>
                    <p><strong>Código:</strong> {{ $referenciaSeleccionada['codigo_rsf'] ?? '' }}</p>
                    <p><strong>Rubro:</strong> {{ $referenciaSeleccionada['tipo_txt'] ?? '' }}</p>
                    <p><strong>Precio:</strong> {{ $referenciaSeleccionada['precio_lista'] ?? '' }}</p>
                    <p><strong>Marca:</strong> {{ $referenciaSeleccionada['marca_rsf'] ?? '' }}</p>
                    <p><strong>Modulo de Venta:</strong> {{ $referenciaSeleccionada['modulo_venta'] ?? '' }}</p>
                    <p><strong>Descripcion:</strong> {{ $referenciaSeleccionada['descripcion'] ?? '' }}</p>
                </div>
            @endif

            <div class="flex justify-end gap-2">
                <button
                    wire:click="confirmarCreacionArticulo"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                >
                    Confirmar creación
                </button>
                <button
                    wire:click="$set('mostrarModal', false)"
                    class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500"
                >
                    Cancelar
                </button>
            </div>
        </div>
    </x-modal>



</div>
