<?php

namespace Migrator\Http\Livewire\Migration;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Migrator\Service\SafeMigrate;
use Illuminate\Support\Facades\Schema;

class Read extends Component
{

    protected $listeners = ['migrationUpdated'];

    public function migrationUpdated()
    {
        // just to update the list
    }

    public function migrate($safe = false): void
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

    public function fresh($withSeed = false): void
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

    public function render(): View
    {
        if (!Schema::hasTable(config('database.migrations'))){
            Artisan::call('migrate:install');
        }

        $migrations = File::files(database_path('migrations'));

        return view('migrator::livewire.migration.read', ['migrations' => $migrations])
            ->layout('migrator::layout', ['title' => 'Migration List']);
    }

    private function storeMessage(string $output, string $type): void
    {
        session()->flash('message', [
            'message' => Str::replace("\n", '<br>', $output),
            'type' => $type
        ]);
    }

}
