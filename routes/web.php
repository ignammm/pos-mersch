<?php

use App\Livewire\articulos\ArticuloCreate;
use App\Livewire\articulos\ArticuloEdit;
use App\Livewire\articulos\ArticuloIndex;
use App\Livewire\Clientes\ClienteCreate;
use App\Livewire\Clientes\ClienteEdit;
use App\Livewire\Clientes\ClientesIndex;
use App\Livewire\Ingresos\IngresoCreate;
use App\Livewire\Pedidos\PedidoCreate;
use App\Livewire\Pedidos\PedidosIndex;
use App\Livewire\Ventas\VentaCreate;
use App\Livewire\Ventas\VentasIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', ArticuloIndex::class);

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/articulos', ArticuloIndex::class)->name('articulos.index');
    Route::get('/articulos/crear', ArticuloCreate::class)->name('articulos.create');
    Route::get('/articulos/{id}/edit', ArticuloEdit::class)->name('articulos.edit');

    Route::get('/ingresos/create', IngresoCreate::class)->name('ingresos.create');

    Route::get('/clientes', ClientesIndex::class)->name('clientes.index');
    Route::get('/clientes/create', ClienteCreate::class)->name('clientes.create');
    Route::get('/clientes/{cliente}/edit', ClienteEdit::class)->name('clientes.edit');

    Route::get('/ventas', VentasIndex::class)->name('ventas.index');
    Route::get('/ventas/create', VentaCreate::class)->name('ventas.create');

    Route::get('/pedidos', PedidosIndex::class)->name('pedidos.index');
    Route::get('/pedidos/create', PedidoCreate::class)->name('pedidos.create');
});