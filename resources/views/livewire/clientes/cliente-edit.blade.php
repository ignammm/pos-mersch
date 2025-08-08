<div>
    <div class="flex items-center gap-2">
        <x-button onclick="window.history.back()">
            <svg class="w-4 h-4 text-gray-1200 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
            </svg>
        </x-button>

        <p class="text-3xl font-bold text-gray-900 dark:text-black underline pb-1 px-2">Editar Cliente</p>
    </div>

    {{-- Mensaje de éxito --}}
    @if (session()->has('message'))
        <div 
            x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 2000)" 
            x-show="show"
            class="p-3 my-4 text-sm text-green-800 rounded-lg bg-green-100"
        >
            {{ session('message') }}
        </div>
    @endif

    {{-- Errores --}}
    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul class="text-sm list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="update" class="pt-12">

        <div class="grid md:grid-cols-3 md:gap-6">
            
            <div class="mb-5">
                <label for="nombre" class="block text-sm text-gray-700">Nombre</label>
                <input wire:model.blur="nombre" id="nombre" type="text" class="w-full mt-1 rounded-md border-gray-300 shadow-sm" required>
            </div>
            
            <div class="mb-5">
                <label for="dni" class="block text-sm text-gray-700">Documento</label>
                <input wire:model.blur="dni" id="dni" type="number" class="w-full mt-1 rounded-md border-gray-300 shadow-sm">
            </div>

            <div class="mb-5">
                <label for="tipo_cliente" class="block text-sm text-gray-700">Tipo de Cliente</label>
                <select wire:model="tipo_cliente" id="tipo_cliente" required
                    class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <option value="">Seleccionar...</option>
                    <option value="Empresa">Empresa</option>
                    <option value="Taller">Taller</option>
                    <option value="Particular">Particular</option>
                </select>
            </div> 

        </div>

       
        <div class="grid md:grid-cols-2 md:gap-6">
          
            <div class="mb-5">
                <label for="percepcion_iva" class="block text-sm text-gray-700">Percepcion Frente al Iva</label>
                <select wire:model="percepcion_iva" id="percepcion_iva" required
                    class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <option value="">Seleccionar...</option>
                    <option value="Responsable Inscripto">Responsable Inscripto</option>
                    <option value="Exento">Exento</option>
                    <option value="Monotributista">Monotributista</option>
                    <option value="Consumidor Final">Consumidor Final</option>
                </select>
            </div>


            <div class="mb-5">
                <label for="cuit_cuil" class="block text-sm text-gray-700">Cuit</label>
                <input wire:model="cuit_cuil" id="cuit_cuil" type="number" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            </div>
        
        </div>

        <div class="grid md:grid-cols-3 md:gap-6">

            <div class="mb-5">
                <label for="telefono" class="block text-sm text-gray-700">Telefono</label>
                <input wire:model="telefono" id="telefono" type="tel" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" >
            </div>

            <div class="mb-5">
                <label for="direccion" class="block text-sm text-gray-700">Direccion</label>
                <input wire:model="direccion" id="direccion" type="text" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            </div>

            <div class="mb-5">
                <label for="email" class="block text-sm text-gray-700">E-mail</label>
                <input wire:model="email" id="email" type="email" class="w-full mt-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            </div>  
            
        </div>

        
        <div class="flex items-center gap-4 mt-4">
            <x-button type="submit" class="text-white focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
                Actualizar
            </x-button>

            <div x-data>
                <x-button 
                    type="button"
                    x-on:click="if (confirm('¿Estás seguro de que deseas eliminara a este cliente?')) { $wire.delete() }"
                    class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5"
                >
                    Eliminar
                </x-button>
            </div>
        </div>

    </form>
</div>
