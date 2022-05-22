<?php

namespace Migrator\Http\Livewire\Migration;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Migrator\Http\Traits\Paginate;
use Migrator\Service\MigratorParser;
use Migrator\Service\SafeMigrate;

/**
 * Class Read
 * @package Migrator\Http\Livewire\Migration
 */
class Read extends Component
{
    use Paginate, WithPagination;

    /**
     * @var string search name for migration
     */
    public $search;

    /**
     * @var string[] List of listeners.
     */
    protected $listeners = ['migrationUpdated'];

    /**
     * migrationUpdated stub
     */
    public function migrationUpdated()
    {
        // just to update the list
    }

    /**
     * Run migrations.
     *
     * @param bool $safe
     */
    public function migrate($safe = false)
    {
        try{
            Artisan::call('migrate');
            $output = Artisan::output();
            $type = 'success';
        } catch (\Exception $exception){
            if ($safe and Str::contains($exception->getMessage(), 'errno: 150')){
                $safeMigrator = (new SafeMigrate($exception->getMessage()))->execute();
                $output = $safeMigrator['message'];
                $type = $safeMigrator['type'];
            } else {
                $output = $exception->getMessage();
                $type = 'error';
            }
        }

        $this->storeMessage($output, $type);

        $this->redirect(route('migrator.read'));
    }

    /**
     * Drop all tables and rerun the migrations.
     * You can also optionally run the seeds.
     *
     * @param bool $withSeed
     */
    public function fresh($withSeed = false)
    {
        $args = $withSeed ? ['--seed' => true] : [];

        try{
            Artisan::call('migrate:fresh', $args);
            $output = Artisan::output();
            $type = 'success';
        } catch (\Exception $exception){
            $output = $exception->getMessage();
            $type = 'error';
        }

        $this->storeMessage($output, $type);

        $this->redirect(route('migrator.read'));
    }

    /**
     * Render Page.
     *
     * @return mixed
     */
    public function render()
    {
        if (!Schema::hasTable(config('database.migrations'))){
            Artisan::call('migrate:install');
        }

        $migrations = $this->getMigrationsForView();
        $migrations = $this->filterMigrationsBySearchValue($migrations);
        $migrations =  $this->withPaginate($migrations);

        return view('migrator::livewire.migration.read', ['migrations' => $migrations])
            ->layout('migrator::layout', ['title' => 'Migration List']);
    }

    /**
     * Flash the messages into the session.
     *
     * @param string $output
     * @param string $type
     */
    private function storeMessage(string $output, string $type)
    {
        session()->flash('message', [
            'message' => Str::replace("\n", '<br>', $output),
            'type' => $type
        ]);
    }

    /**
     * Get the list of all migration paths, including the custom migration directories.
     *
     * @return array
     */
    private static function migrationDirs()
    {
        $migrationDirs = [];
        $migrationDirs[] = app()->databasePath().DIRECTORY_SEPARATOR.'migrations';

        foreach (app('migrator')->paths() as $path) {
            $migrationDirs[] = $path;
        }

        return $migrationDirs;
    }

    /**
     * Wrapper to Paginate the input data.
     *
     * @param array|Collection $data
     * @return LengthAwarePaginator
     */
    private function withPaginate($data)
    {
        $perPage = config('migrator.per_page', 10);
        $path = config('migrator.route', 'migrator');

        return $this->paginate($data, $perPage)->withPath($path);
    }

    /**
     * Get the list of all migrations in the app, including those in the custom migration directories.
     *
     * @return array
     */
    private function getMigrationsForView()
    {
        $migrations = [];

        foreach (self::migrationDirs() as $dir) {
            $migrations = array_merge(File::files($dir), $migrations);
        }

        return $migrations;
    }

    /*
     * filter migrations by search value
     *
     * @return array
     */
    public function filterMigrationsBySearchValue($migrations) : array
    {
        if (!empty($this->search)){
            return collect($migrations)->filter(function ($migration) {
                $migratorParser = resolve(MigratorParser::class , ['migration' => $migration]);
                if (Str::contains($migratorParser->getName(),$this->search) ){
                    return $migration;
                }
            })->toArray();
        }
        return $migrations;
    }
}
