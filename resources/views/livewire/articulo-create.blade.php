<div>
    
   <div class="flex items-center gap-2">
        <x-button  onclick="window.history.back()">
            <svg class="w-4 h-4 text-gray-1200 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
            </svg>
        </x-button>

        <p class="text-3xl font-bold text-gray-900 dark:text-black underline pb-1 px-2">Nuevo artículo</p>

    </div>

    <form wire:submit.prevent="submit" class="items-center justify-center pt-12">
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

        <div class="grid md:grid-cols-3 md:gap-6">

            <div class="mb-5">
                <label for="codigo_proveedor" class="block text-sm text-gray-700">Código</label>
                <input wire:model.debounce.500ms="codigo_proveedor" id="codigo_proveedor" type="text" class="w-full mt-1 rounded-md border-gray-300 shadow-sm">
            </div>
                        
            <div class="mb-5">
                <label for="articulo" class="block text-sm text-gray-700">Articulo</label>
                <input wire:model.defer="articulo" id="articulo" type="text" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
            </div>

            <div class="mb-5">
                <label for="rubro" class="block text-sm text-gray-700">Rubro</label>
                <input wire:model.defer="rubro" id="rubro" type="text" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
            </div>

        </div>
        
        <div class="grid md:grid-cols-4 md:gap-6">
            <div class="mb-5">
                <label for="proveedor_id" class="block text-sm text-gray-700">Proveedor</label>
                <input wire:model.defer="proveedor_id" id="proveedor_id" type="text" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
            </div>

            <div class="mb-5">
                <label for="marca" class="block text-sm text-gray-700">Marca</label>
                <input wire:model.defer="marca" id="marca" type="text" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
            </div>
            
            <div class="mb-5">
                <label for="precio" class="block text-sm text-gray-700">Precio</label>
                <input wire:model.defer="precio" id="precio" type="number" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            </div>
            
            <div class="mb-5">
                <label for="unidad" class="block text-sm text-gray-700">Unidad de venta</label>
                <input wire:model.defer="unidad" id="unidad" type="number" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
            </div>
        </div>
        
        <div class="mb-5">
            <label for="descripcion" class="block text-sm text-gray-700">Descripcion</label>
            <textarea wire:model.defer="descripcion" id="descripcion" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
        </div>
        
        <div class="mt-4">
            <x-button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                Guardar
            </x-button>

        </div>
    </form>


</div>
