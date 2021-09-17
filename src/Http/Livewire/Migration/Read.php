<?php

namespace Migrator\Http\Livewire\Migration;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class Read extends Component
{

    protected $listeners = ['migrationUpdated'];

    public function migrationUpdated()
    {
        // just to update the list
    }

    public function migrate()
    {
        Artisan::call('migrate');

        $output = Artisan::output();

        session()->flash('message', Str::replace("\n", '<br>', $output));

        $this->redirect(route('migrator.read'));
    }

    public function fresh($withSeed = false)
    {
        $args = $withSeed ? ['--seed' => true] : [];

        Artisan::call('migrate:fresh', $args);

        $output = Artisan::output();

        session()->flash('message', Str::replace("\n", '<br>', $output));

        $this->redirect(route('migrator.read'));
    }

    public function render()
    {
        $migrations = File::files(database_path('migrations'));

        return view('migrator::livewire.migration.read', ['migrations' => $migrations])
            ->layout('migrator::layout', ['title' => 'Migration List']);
    }

}
