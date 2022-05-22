<?php

namespace Migrator\Http\Livewire\Migration;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Migrator\Service\MigratorParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Single
 * @package Migrator\Http\Livewire\Migration
 */
class Single extends Component
{
    /**
     * @var string Migration file Path
     */
    public $migrationPath;

    /**
     * @var string Migration file
     */
    public $migrationFile;

    /**
     * @var string Migration name
     */
    public $migrationName;

    /**
     * @var string Migration connection name
     */
    public $migrationConnectionName;

    /**
     * @var string Migration creation date difference from today in a human-readable format
     */
    public $migrationCreatedAt;

    /**
     * @var int Migration batch count.
     */
    public $batch;

    /**
     * @var array Migration file sctructured content
     */
    public $structure;

    /**
     * Component mount.
     * @param SplFileInfo $migration
     */
    public function mount(SplFileInfo $migration)
    {
        $this->migrationPath = $migration->getPathname();
        $this->migrationFile = $migration->getFilename();
        $migratorParser = new MigratorParser($migration);
        $this->migrationName = $migratorParser->getName();
        $this->migrationConnectionName = $migratorParser->getConnectionName();
        $this->migrationCreatedAt = $migratorParser->getDate();
        $this->batch = DB::table(config('database.migrations'))
            ->where('migration', str_replace('.php', '', $this->migrationFile))
            ->first(['batch'])->batch ?? 0;
        $this->structure = $migratorParser->getStructure();
    }

    /**
     * Run migration command.
     */
    public function migrate()
    {
        try {
            \Artisan::call('migrate', [
                '--path' => $this->getPath()
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

    /**
     * Refresh migrations.
     */
    public function refresh()
    {
        \Artisan::call('migrate:refresh', [
            '--path' => $this->getPath()
        ]);

        $this->dispatchBrowserEvent('show-message', [
            'type' => 'success',
            'message' => 'Migration was refreshed.'
        ]);

        $this->emit('migrationUpdated');
    }

    /**
     * Roll back all database migrations.
     */
    public function removeTable()
    {
        \Artisan::call('migrate:reset', [
            '--path' => $this->getPath(),
            '--force' => true,
        ]);

        $this->dispatchBrowserEvent('show-message', [
            'type' => 'success',
            'message' => 'Table was dropped.'
        ]);

        $this->emit('migrationUpdated');
    }

    /**
     * Delete a migration.
     */
    public function deleteMigration()
    {
        $this->removeTable();

        File::delete($this->migrationPath);

        $this->emit('migrationUpdated');
    }

    /**
     * Roll back a specific migration.
     */
    public function rollback()
    {
        $migrationTable = config('database.migrations');
        \DB::table($migrationTable)
            ->where('migration', str_replace('.php', '', $this->migrationFile))
            ->update(['batch' => \DB::table($migrationTable)->max('batch')]);

        try {
            \Artisan::call('migrate:rollback', [
                '--path' => $this->getPath(),
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

    /**
     * Render the page.
     *
     * @return View
     */
    public function render()
    {
        return view('migrator::livewire.migration.single');
    }

    /**
     * Get the migration path.
     *
     * @return string
     */
    private function getPath()
    {
        return str_replace(base_path(), '', $this->migrationPath);
    }

}
