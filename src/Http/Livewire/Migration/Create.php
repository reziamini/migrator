<?php

namespace Migrator\Http\Livewire\Migration;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;

class Create extends Component
{
    public $name;
    public $table;
    public $connection;
    public $type = 'create';

    public function mount(): void
    {
        $this->connection = config('database.default');
    }

    public function create(): void
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

        if ($this->connection and $this->connection != config('database.default')) {
            $this->addConnection();
        }

        $this->dispatchBrowserEvent('show-message', [
            'type' => 'success',
            'message' => 'Migration was created.'
        ]);

        $this->reset('name', 'table');
        $this->emit('migrationUpdated');
    }

    public function render(): View
    {
        $connections = array_keys(config('database.connections'));

        return view('migrator::livewire.migration.create', compact('connections'));
    }

    private function addConnection(): void
    {
        $output = Artisan::output();

        $fileName = trim(substr($output, stripos($output, ":") + 1)) . '.php';

        $file = database_path('migrations\\'.$fileName);

        $fileContent = file_get_contents($file);

        $position = stripos($fileContent, "extends Migration") + 20;

        $comment = "    /**\n     * The database connection that should be used by the migration.\n     *\n     * @var string\n     */\n";

        $finalContent = substr($fileContent, 0, $position) . "\n" . $comment . '    protected $connection = ' . "'" . $this->connection . "';\n\n" . substr($fileContent, $position);

        file_put_contents($file, $finalContent);
    }

    protected function getRules()
    {
        return [
            'name' => 'required|min:2',
            'table' => 'required|min:2',
            'connection' => ['required', Rule::in(array_keys(config('database.connections')))],
            'type' => 'required|in:create,edit'
        ];
    }

}
