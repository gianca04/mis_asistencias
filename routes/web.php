<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use Livewire\Livewire;

Livewire::setScriptRoute(function ($handle) {
return Route::get('/monitor/public/livewire/livewire.js', $handle);
});

Livewire::setUpdateRoute(function ($handle) {
return Route::post('/monitor/public/livewire/update', $handle);
});
