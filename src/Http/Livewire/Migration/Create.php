<?php

namespace Migrator\Http\Livewire\Migration;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;

/**
 * Class Create
 * @package Migrator\Http\Livewire\Migration
 */
class Create extends Component
{
    /**
     * @var string Migration name
     */
    public $name;

    /**
     * @var string Table name
     */
    public $table;

    /**
     * @var string Database connection name
     */
    public $connection;

    /**
     * @var string Migration Type
     */
    public $type = 'create';

    /**
     * Component Mount.
     */
    public function mount()
    {
        $this->connection = config('database.default');
    }

    /**
     * Create a migration from user input.
     */
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

    /**
     * Render view.
     *
     * @return View
     */
    public function render()
    {
        $connections = array_keys(config('database.connections'));

        return view('migrator::livewire.migration.create', compact('connections'));
    }

    /**
     * Alter the migration file to add the databsae connection name.
     */
    private function addConnection()
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

    /**
     * Get migration creation validation rules.
     *
     * @return array
     */
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
