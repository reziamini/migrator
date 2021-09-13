<?php

use Illuminate\Support\Facades\Route;

Route::get(config('migrator.route'), \Migrator\Http\Livewire\Migration\Read::class)
    ->name('migrator.read')->middleware('web');
