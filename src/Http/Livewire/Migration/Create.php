<?php

namespace Migrator\Http\Livewire\Migration;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;

class Create extends Component
{
    public $name;
    public $table;
    public $type = 'create';

    protected $rules = [
        'name' => 'required|min:2',
        'table' => 'required|min:2',
        'type' => 'required|in:create,edit'
    ];

    public function create()
    {
        $this->validate();

        $array = [
            'name' => strtolower($this->name),
        ];

        if ($this->type == 'edit') {
            $array['--table'] = $this->table;
        } else {
            $array['--create'] = $this->table;
        }

        Artisan::call('make:migration', $array);

        $this->dispatchBrowserEvent('show-message', [
            'type' => 'success',
            'message' => 'Migration was created.'
        ]);

        $this->reset();
        $this->emit('migrationUpdated');
    }

    public function render()
    {
        return view('migrator::livewire.migration.create');
    }

}
