<div class="flex flex-col min-h-screen">

    <div class="flex items-center">
        <x-button onclick="window.history.back()">
            <svg class="w-4 h-4 text-gray-1200 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
            </svg>
        </x-button>
        <div class="inline-flex items-center px-4 py-2">
            <h1 class="text-3xl font-bold mb-2 ">Detalles de trabajo</h1>
        </div>
    </div>

    <div 
        x-data="{ mostrar: false }" 
        x-init="@this.on('trabajo-update', () => { 
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
            ✅ Trabajo actualizado correctamente
        </div>
    </div>


    {{-- Tarjeta --}}
    <div class="flex justify-center">
        <div class="mt-10 w-full max-w-2xl p-6 bg-white border border-gray-200 rounded-2xl shadow-lg sm:p-10 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                {{-- Izquierda --}}
                <div class="flex-1 min-w-0">
                    <h5 class="text-2xl font-bold leading-none text-gray-900 dark:text-white">
                        {{ $trabajo->nombre }}
                    </h5>
                    <p class="text-base text-gray-500 truncate dark:text-gray-400">
                        {{ $trabajo->descripcion }}
                    </p>
                </div>
    
                {{-- Derecha --}}
                <div class="ml-6">
                    <h5 class="text-base font-medium text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($trabajo->fecha)->format('d/m/Y')  }}
                    </h5>
                </div>
            </div>
    
            <div class="flow-root">
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($detalles_trabajo as $dt)
                        <li class="py-4">
                            <div class="flex items-center">
                                <div class="flex-1 min-w-0 ms-4">
                                    <p class="text-base font-medium text-gray-900 truncate dark:text-white">
                                        {{ $dt->articulo->articulo }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                        {{ $dt->articulo->rubro }}
                                    </p>
                                </div>
                                <div class="inline-flex items-center text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $dt->cantidad }}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- Botón debajo --}}
    <div class="flex justify-center mt-6">

        <a href="{{ route('trabajos.edit', $trabajo) }}"
        class=" flex p-1 mr-4 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg shadow-md">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>

            Editar
        </a>
        
        <x-button 
            wire:click="eliminarTrabajo"
            wire:confirm.prompt="¿Seguro que quieres eliminar este trabajo?\n\nTenga en cuenta que no se devolverá el stock a los artículos.\n\nEscribe 'ELIMINAR' para confirmar|ELIMINAR"
            class="flex p-1 bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-lg shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
            Borrar
        </x-button>
    </div>
</div>


