<?php

use App\Livewire\ArticuloCreate;
use App\Livewire\ArticuloIndex;
use App\Livewire\articulos\ArticuloEdit;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
});