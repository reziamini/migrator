<?php

namespace Migrator\Http\Livewire\Migration;

use Livewire\Component;
use Migrator\Service\MigratorParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class Single extends Component
{

    public $migrationFile;
    public $migrationName;
    public $migrationCreatedAt;
    public $status;

    public function mount($migration)
    {
        $this->migrationFile = $migration->getFilename();
        $object = new MigratorParser($this->migrationFile);
        $this->migrationName = $object->getName();
        $this->migrationCreatedAt = $object->getDate();
        $this->status = DB::table('migrations')->where('migration', str_replace('.php', '', $this->migrationFile))->exists() ? 'Yes' : 'No';
    }

    public function migrate()
    {
        $path = 'database/migrations/'.$this->migrationFile;

        \Artisan::call('migrate', [
            '--path' => $path
        ]);

        $this->emit('migrationUpdated');
    }

    public function refresh()
    {
        $path = 'database/migrations/'.$this->migrationFile;

        \Artisan::call('migrate:refresh', [
            '--path' => $path
        ]);

        $this->emit('migrationUpdated');
    }

    public function removeTable()
    {
        $path = 'database/migrations/'.$this->migrationFile;

        \Artisan::call('migrate:reset', [
            '--path' => $path,
            '--force' => true,
        ]);

        $this->emit('migrationUpdated');
    }

    public function deleteMigration()
    {
        $this->removeTable();

        $path = database_path('migrations/'.$this->migrationFile);

        File::delete($path);

        $this->emit('migrationUpdated');
    }

    public function render()
    {
        return view('migrator::livewire.migration.single');
    }

}
