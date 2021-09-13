<?php

\Illuminate\Support\Facades\Route::get(config('migrator.route'), \Migrator\Http\Livewire\Migration\Read::class)
    ->name('migrator.read');
