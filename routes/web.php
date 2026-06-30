<?php

use App\Livewire\Animals;
use App\Livewire\Npcs;
use App\Livewire\Organizations;
use App\Livewire\Party;
use App\Livewire\Pcs;
use App\Livewire\Planets;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('party'))->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/party', Party\Index::class)->name('party');

    Route::get('/pcs', Pcs\Index::class)->name('pcs.index');
    Route::get('/pcs/create', Pcs\Create::class)->name('pcs.create');
    Route::get('/pcs/{character}/edit', Pcs\Edit::class)->name('pcs.edit');

    Route::get('/npcs', Npcs\Index::class)->name('npcs.index');
    Route::get('/npcs/create', Npcs\Create::class)->name('npcs.create');
    Route::get('/npcs/{npc}/edit', Npcs\Edit::class)->name('npcs.edit');

    Route::get('/organizations', Organizations\Index::class)->name('organizations.index');
    Route::get('/organizations/create', Organizations\Create::class)->name('organizations.create');
    Route::get('/organizations/{organization}/edit', Organizations\Edit::class)->name('organizations.edit');

    Route::get('/planets', Planets\Index::class)->name('planets.index');
    Route::get('/planets/create', Planets\Create::class)->name('planets.create');
    Route::get('/planets/{planet}/edit', Planets\Edit::class)->name('planets.edit');

    Route::get('/animals', Animals\Index::class)->name('animals.index');
    Route::get('/animals/create', Animals\Create::class)->name('animals.create');
    Route::get('/animals/{animal}/edit', Animals\Edit::class)->name('animals.edit');
});

require __DIR__.'/settings.php';
