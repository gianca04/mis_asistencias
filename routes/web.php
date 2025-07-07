<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

use Livewire\Livewire;

Livewire::setScriptRoute(function ($handle) {
return Route::get('/mis_asistencias/public/livewire/livewire.js', $handle);
});

Livewire::setUpdateRoute(function ($handle) {
return Route::post('/mis_asistencias/public/livewire/update', $handle);
});
