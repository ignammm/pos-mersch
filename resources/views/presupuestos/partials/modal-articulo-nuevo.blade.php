<div>
    <x-modal wire:model.live="mostrarModalDuplicados">
        <div class="p-6 bg-gray-50 min-h-screen">
            <h2 class="text-3xl font-bold mb-6 text-gray-800">Seleccione su artículo</h2>

            {{-- Artículos existentes --}}
            @if ($coincidenciasArt && $coincidenciasArt->isNotEmpty())
                <h3 class="text-xl font-bold text-gray-800 mb-4">Artículos existentes</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach ($coincidenciasArt as $art)
                        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition p-5 flex flex-col justify-between">
                            <div class="space-y-2">
                                <p class="text-gray-700"><span class="font-semibold">Artículo:</span> {{ $art->articulo ?? '' }}</p>
                                <p class="text-blue-800"><span class="font-semibold">Marca:</span> {{ $art->marca ?? '' }}</p>
                                <p class="text-gray-700"><span class="font-semibold">Rubro:</span> {{ $art->rubro ?? '' }}</p>
                                <p class="text-gray-700"><span class="font-semibold">Precio:</span> {{ $art->precio ?? '' }}</p>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button
                                    wire:click="confirmarSeleccionArt({{ $art->id }})"
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition"
                                >
                                    Seleccionar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Separador --}}
            @if (($coincidenciasArt && $coincidenciasArt->isNotEmpty()) && ($coincidenciasRef && $coincidenciasRef->isNotEmpty()))
                <hr class="my-6 border-gray-300">
            @endif

            {{-- Artículos nuevos --}}
            @if ($coincidenciasRef && $coincidenciasRef->isNotEmpty())
                <h3 class="text-xl font-bold text-gray-800 mb-4">Artículos nuevos</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($coincidenciasRef as $ref)
                        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition p-5 flex flex-col justify-between">
                            <div class="space-y-2">
                                <p class="text-gray-700"><span class="font-semibold">Artículo:</span> {{ $ref->articulo ?? '' }}</p>
                                <p class="text-blue-800"><span class="font-semibold">Marca:</span> {{ $ref->marca_rsf ?? '' }}</p>
                                <p class="text-gray-700"><span class="font-semibold">Rubro:</span> {{ $ref->tipo_txt ?? '' }}</p>
                                <p class="text-gray-700"><span class="font-semibold">Precio:</span> {{ $ref->precio_lista ?? '' }}</p>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button
                                    wire:click="confirmarSeleccionRef({{ $ref->id }})"
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition"
                                >
                                    Seleccionar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Botón cancelar --}}
            <div class="flex justify-end mt-8">
                <button
                    wire:click="$set('mostrarModalDuplicados', false)"
                    class="bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-600 transition"
                >
                    Cancelar
                </button>
            </div>
        </div>
    </x-modal>
</div>