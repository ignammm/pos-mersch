<div class="flex flex-col min-h-screen">

    <div class="flex items-center">
        <x-button onclick="window.location.href='{{ route('presupuestos.index') }}'" >
            <svg class="w-4 h-4 text-gray-1200 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
            </svg>
        </x-button>
        <div class="inline-flex items-center px-4 py-2">
            <h1 class="text-3xl font-bold mb-2 ">Detalles de presupuesto</h1>
        </div>
    </div>

    <div 
        x-data="{ mostrar: false }" 
        x-init="@this.on('presupuesto-update', () => { 
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
            ✅ Presupuesto actualizado correctamente
        </div>
    </div>


    {{-- Tarjeta --}}
    <div class="flex justify-center">
        <div class="mt-10 w-full max-w-2xl p-6 bg-white border border-gray-200 rounded-2xl shadow-lg sm:p-10 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                {{-- Izquierda --}}
                <div class="flex-1 min-w-0">
                    <h5 class="text-2xl font-bold leading-none text-gray-900 dark:text-white">
                        {{ $presupuesto->numero }}
                    </h5>
                    <p class="text-base text-gray-500 truncate dark:text-gray-400">
                        {{ $presupuesto->observaciones }}
                    </p>
                </div>
    
                {{-- Derecha --}}
                <div class="ml-6">
                    <h5 class="text-base font-medium text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($presupuesto->fecha_emision)->format('d/m/Y')  }}
                    </h5>
                </div>
            </div>
    
            <div class="flow-root">
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($detalles_presupuesto as $dp)
                        <li class="py-4">
                            <div class="flex items-center">
                                <div class="flex-1 min-w-0 ms-4">
                                    <p class="text-base font-medium text-gray-900 truncate dark:text-white">
                                        {{ $dp->articulo->articulo }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                        {{ $dp->articulo->rubro }}
                                    </p>
                                </div>
                                <div class="inline-flex items-center text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $dp->cantidad }}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            
                <hr>
                
                <div class="mt-6 flex justify-end">
                    <span class="px-4 py-2 rounded-full text-lg font-semibold bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200 shadow">
                        Total: ${{ number_format($total, 2, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Botón debajo --}}
    <div class="flex justify-center mt-6">
        
        @if ($presupuesto->estado == 'pendiente')
            
            <a href="{{ route('presupuestos.edit', $presupuesto) }}"
            class=" flex p-1 mr-4 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 text-xs shadow-md  rounded-md font-semibold uppercase items-center">

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>

                <div class="pl-1">
                    Editar
                </div>
            </a>

        @endif

        <x-button 
            wire:click="eliminarPresupuesto"
            wire:confirm.prompt="¿Seguro que quieres eliminar este presupuesto?\n\nEscribe 'ELIMINAR' para confirmar|ELIMINAR"
            class="mr-4 flex p-1 bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-lg shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>

            <div class="pl-1">
                Borrar
            </div>
        </x-button>

        @if ($presupuesto->estado != 'aceptado' && $presupuesto->estado != 'rechazado')
            
             
            <x-button 
                wire:click="presupuestoRechazar"
                wire:confirm.prompt="¿Seguro que quieres rechazar este presupuesto?\n\nTenga en cuenta que no se podrá volver a modificar.\n\nEscribe 'RECHAZAR' para confirmar|RECHAZAR"
                class="mr-4 flex p-1 bg-yellow-500 hover:bg-yellow-700 text-white px-5 py-3 rounded-lg shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>


                <div class="pl-1">
                    Rechazar 
                </div>
            </x-button>

            <x-button 
                wire:click="presupuestoVenta"
                wire:confirm.prompt="¿Seguro que quieres finalizar este presupuesto?\n\nTenga en cuenta que no se podrá volver a modificar y generara una venta.\n\nEscribe 'VENTA' para confirmar|VENTA"
                class="mr-4 flex p-1 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>


                <div class="pl-1">
                    Venta 
                </div>
            </x-button>

            <x-button 
                wire:click="presupuestoTrabajo"
                wire:confirm.prompt="¿Seguro que quieres finalizar este presupuesto?\n\nTenga en cuenta que no se podrá volver a modificar y generara un presupuesto.\n\nEscribe 'TRABAJO' para confirmar|TRABAJO"
                class="flex p-1 bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-lg shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                </svg>



                <div class="pl-1">
                    Trabajo 
                </div>
            </x-button>
            
        @endif
    </div>
</div>


