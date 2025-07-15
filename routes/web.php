<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

use Livewire\Livewire;

Livewire::setScriptRoute(function ($handle) {
return Route::get('/mis.asistencias.sat-industriales.pe/public/livewire/livewire.js', $handle);
});

Livewire::setUpdateRoute(function ($handle) {
return Route::post('/mis.asistencias.sat-industriales.pe/public/livewire/update', $handle);
});

Route::get('/crear-symlink', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');

    if (file_exists($link)) {
        return '⚠️ Ya existe un enlace o carpeta llamado "storage" en public.';
    }

    if (symlink($target, $link)) {
        return '✅ Enlace simbólico creado correctamente.';
    } else {
        return '❌ No se pudo crear el enlace simbólico.';
    }
});
