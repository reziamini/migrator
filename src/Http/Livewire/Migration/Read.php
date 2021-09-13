<?php

namespace Migrator\Http\Livewire\Migration;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class Read extends Component
{

    protected $listeners = ['migrationUpdated' => 'migrationUpdated'];

    public function migrationUpdated()
    {
        // just to update the list
    }

    public function render()
    {
        $migrations = File::files(database_path('migrations'));

        return view('migrator::livewire.migration.read', ['migrations' => $migrations])
            ->layout('migrator::layout', ['title' => 'Migration List']);
    }

}
