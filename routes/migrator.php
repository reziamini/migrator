<?php

use Illuminate\Support\Facades\Route;
use Migrator\Http\Livewire\Migration\Read;

Route::get(config('migrator.route'), Read::class)
    ->name('migrator.read')->middleware(
        !config('migrator.middleware') ? ['web'] : array_unique(array_merge(['web'], (array) config('migrator.middleware')))
    );
