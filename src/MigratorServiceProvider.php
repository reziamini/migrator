<?php

namespace Migrator;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Migrator\Http\Livewire\Migration\Read;
use Migrator\Http\Livewire\Migration\Create;
use Migrator\Http\Livewire\Migration\Single;

class MigratorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/migrator.php', 'migrator');
    }

    public function boot()
    {
        $local = $this->app->environment('local');
        $only = config('migrator.local', true);

        if ($local || ! $only) {
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'migrator');
            $this->loadRoutesFrom(__DIR__.'/../routes/migrator.php');

            $this->publishes([
                __DIR__.'/../config' => config_path()
            ], 'migrator-config');

            $this->registerLivewireComponents();
        }
    }

    private function registerLivewireComponents()
    {
        Livewire::component('migrator::livewire.migration.create', Create::class);
        Livewire::component('migrator::livewire.migration.read', Read::class);
        Livewire::component('migrator::livewire.migration.single', Single::class);
    }
}
