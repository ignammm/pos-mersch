<div>
    
   <div class="flex items-center gap-2">
        <x-button  onclick="window.history.back()">
            <svg class="w-4 h-4 text-gray-1200 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
            </svg>
        </x-button>

        <p class="text-3xl font-bold text-gray-900 dark:text-black underline pb-1 px-2">Nuevo artículo</p>

    </div>

    <form wire:submit="submit" class="items-center justify-center pt-12">
        @csrf

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

       <div 
            x-data="{ mostrar: false }" 
            x-init="@this.on('articulo-creado', () => { 
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
                ✅ Artículo creado correctamente
            </div>
        </div>


       
        <div class="grid md:grid-cols-3 md:gap-6">
        
            <div class="mb-5">
                <label for="proveedor_id" class="block text-sm text-gray-700">Proveedor</label>
                <select wire:model="proveedor_id" id="proveedor_id" required
                    class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <option value="">Seleccione un proveedor</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-5">
                <label for="codigo_proveedor" class="block text-sm text-gray-700">Código Proveedor</label>
                <input wire:model.blur="codigo_proveedor" id="codigo_proveedor" type="text" class="w-full mt-1 rounded-md border-gray-300 shadow-sm" required>
            </div>

            <div class="mb-5">
                <label for="codigo_fabricante" class="block text-sm text-gray-700">Código Fabricante</label>
                <input wire:model.blur="codigo_fabricante" id="codigo_fabricante" type="text" class="w-full mt-1 rounded-md border-gray-300 shadow-sm" required>
            </div>

        </div>
        
        <div class="grid md:grid-cols-2 md:gap-6">

          

            <div class="mb-5">
                <label for="articulo" class="block text-sm text-gray-700">Articulo</label>
                <input wire:model="articulo" id="articulo" type="text" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
            </div>


            
            <div class="mb-5">
                <label for="rubro" class="block text-sm text-gray-700">Rubro</label>
                <input wire:model="rubro" id="rubro" type="text" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
            </div>
            
        </div>
        <div class="grid md:grid-cols-4 md:gap-6">

              
            <div class="mb-5">
                <label for="marca" class="block text-sm text-gray-700">Marca</label>
                <input wire:model="marca" id="marca" type="text" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
            </div>  
            
            <div class="mb-5">
                <label for="enlace" class="block text-sm text-gray-700">Enlace</label>
                <input wire:model="enlace" id="enlace" type="text" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                @error('codigo_proveedor') <span class="text-red-500 text-sm">Advertencia: este campo debe rellenarse con codigo_fabricante/codigo_proveedor</span> @enderror
            </div>
            
            <div class="mb-5">
                <label for="unidad" class="block text-sm text-gray-700">Unidad de venta</label>
                <input wire:model="unidad" id="unidad" type="number" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
            </div>
            
            <div class="mb-5">
                <label for="precio" class="block text-sm text-gray-700">Precio</label>
                <input wire:model="precio" id="precio" type="number" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            </div>
            
            
        </div>

        <div class="mb-5">
            <label for="descripcion" class="block text-sm text-gray-700">Descripcion</label>
            <textarea wire:model="descripcion" id="descripcion" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
        </div>
        
        <div class="mt-4">
            <x-button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                Guardar
            </x-button>

        </div>
    </form>


</div>
