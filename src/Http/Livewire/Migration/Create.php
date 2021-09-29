<?php

namespace Migrator\Http\Livewire\Migration;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;

class Create extends Component
{
    public $name;
    public $table;
    public $connection;
    public $type = 'create';

    protected $rules = [
        'name' => 'required|min:2',
        'table' => 'required|min:2',
        'connection' => 'nullable|min:2',
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

        if ($this->connection) {
            $this->addConnection();
        }

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

    private function addConnection()
    {
        $output = Artisan::output();

        $fileName = trim(substr($output, stripos($output, ":") + 1)) . '.php';

        $file = database_path('migrations\\'.$fileName);

        $fileContent = file_get_contents($file);

        $position = stripos($fileContent, "extends Migration") + 20;

        $finalContent = substr($fileContent, 0, $position) . "\n" . '    protected $connection = ' . "'" . $this->connection . "';\n" . substr($fileContent, $position);
        
        file_put_contents($file, $finalContent);
    }
}
