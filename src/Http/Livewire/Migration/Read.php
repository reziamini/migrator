<?php

namespace Migrator\Http\Livewire\Migration;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Migrator\Http\Traits\Paginate;
use Migrator\Service\SafeMigrate;

class Read extends Component
{
    use Paginate, WithPagination;

    protected $listeners = ['migrationUpdated'];

    public function migrationUpdated()
    {
        // just to update the list
    }

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

    public function render()
    {
        if (!Schema::hasTable(config('database.migrations'))){
            Artisan::call('migrate:install');
        }

        $migrations = $this->getMigrationsForView();

        return view('migrator::livewire.migration.read', ['migrations' => $migrations])
            ->layout('migrator::layout', ['title' => 'Migration List']);
    }

    private function storeMessage(string $output, string $type)
    {
        session()->flash('message', [
            'message' => Str::replace("\n", '<br>', $output),
            'type' => $type
        ]);
    }

    private static function migrationDirs()
    {
        $migrationDirs = [];
        $migrationDirs[] = app()->databasePath().DIRECTORY_SEPARATOR.'migrations';

        foreach (app('migrator')->paths() as $path) {
            $migrationDirs[] = $path;
        }

        return $migrationDirs;
    }

    private function withPaginate($data)
    {
        $perPage = config('migrator.per_page', 10);
        $path = config('migrator.route', 'migrator');

        return $this->paginate($data, $perPage)->withPath($path);
    }

    private function getMigrationsForView()
    {
        $migrations = [];

        foreach (self::migrationDirs() as $dir) {
            $migrations = array_merge(File::files($dir), $migrations);
        }

        $migrations = $this->withPaginate($migrations);

        return $migrations;
    }
}
