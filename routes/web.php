<?php

use App\Livewire\articulos\ArticuloCreate;
use App\Livewire\articulos\ArticuloEdit;
use App\Livewire\articulos\ArticuloIndex;
use App\Livewire\Ingresos\IngresoCreate;
use Illuminate\Support\Facades\Route;

Route::get('/', ArticuloIndex::class);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/articulos', ArticuloIndex::class)->name('articulos.index');
});


Route::middleware(['auth','verified'])->group(function () {
    Route::get('/articulos', ArticuloIndex::class)->name('articulos.index');
    Route::get('/articulos/crear', ArticuloCreate::class)->name('articulos.create');
    Route::get('/articulos/{id}/edit', ArticuloEdit::class)->name('articulos.edit');

    Route::get('/ingresos/create', IngresoCreate::class)->name('ingresos.create');

    
});