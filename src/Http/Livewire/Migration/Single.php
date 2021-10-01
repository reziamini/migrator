<?php

namespace Migrator\Http\Livewire\Migration;

use Livewire\Component;
use Migrator\Service\MigratorParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Single extends Component
{

    public $migrationFile;
    public $migrationName;
    public $migrationConnectionName;
    public $migrationCreatedAt;
    public $status;
    public $batch;

    public function mount($migration)
    {
        $this->migrationFile = $migration->getFilename();
        $object = new MigratorParser($this->migrationFile);
        $this->migrationName = $object->getName();
        $this->migrationConnectionName = $object->getConnectionName();
        $this->migrationCreatedAt = $object->getDate();
        $this->status = DB::table('migrations')->where('migration', str_replace('.php', '', $this->migrationFile))->exists() ? 'Yes' : 'No';
        $this->batch = DB::table('migrations')
            ->where('migration', str_replace('.php', '', $this->migrationFile))
            ->first(['batch'])->batch ?? 0;
    }

    public function migrate()
    {
        $path = 'database/migrations/'.$this->migrationFile;

        try {
            \Artisan::call('migrate', [
                '--path' => $path
            ]);
            
            $message = 'Migration was migrated.';
            $type = 'success';
        } catch(\Exception $exception) {
            $message = $exception->getMessage();
            $type = 'error';
        }

        $this->dispatchBrowserEvent('show-message', [
            'type' => $type,
            'message' => Str::replace("\n", '<br>', $message)
        ]);

        $this->emit('migrationUpdated');
    }

    public function refresh()
    {
        $path = 'database/migrations/'.$this->migrationFile;

        \Artisan::call('migrate:refresh', [
            '--path' => $path
        ]);

        $this->dispatchBrowserEvent('show-message', [
            'type' => 'success',
            'message' => 'Migration was refreshed.'
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

        $this->dispatchBrowserEvent('show-message', [
            'type' => 'success',
            'message' => 'Table was dropped.'
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

    public function rollback()
    {
        \DB::table('migrations')
            ->where('migration', str_replace('.php', '', $this->migrationFile))
            ->update(['batch' => \DB::table('migrations')->max('batch')]);

        $path = 'database/migrations/'.$this->migrationFile;

        try {
            \Artisan::call('migrate:rollback', [
                '--path' => $path,
            ]);
            
            $message = 'Migration was rolled back.';
            $type = 'success';
        } catch(\Exception $exception) {
            $message = $exception->getMessage();
            $type = 'error';
        }

        $this->dispatchBrowserEvent('show-message', [
            'type' => $type,
            'message' => Str::replace("\n", '<br>', $message)
        ]);

        $this->emit('migrationUpdated');
    }

    public function render()
    {
        return view('migrator::livewire.migration.single');
    }

}
