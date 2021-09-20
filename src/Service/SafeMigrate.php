<?php


namespace Migrator\Service;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SafeMigrate
{
    protected $table;
    protected $migrations = [];

    public function __construct($error)
    {
        $this->table = $this->renderTableName($error);
    }

    public function renderTableName($error)
    {
        preg_match("/.*references `(\w+)`.*/", $error, $match);

        return $match[1];
    }

    public function getMigrationFiles()
    {
        $migrations = File::glob(database_path("migrations/*{$this->table}*"));

        return $migrations;
    }

    public function getMigrations()
    {
        $migrations = $this->getMigrationFiles();
        $this->migrations[] = $migrations;

        if (is_null($migrations)){
            return "No dependency founded for `{$this->table}` table";
        }

        \Artisan::call('db:wipe', [
            '--force' => true
        ]);

        foreach ($migrations as $migration) {
            try {
                \Artisan::call('migrate', [
                    '--path' => $migration,
                    '--realpath' => true,
                ]);
            } catch (\Exception $exception){
                if (\Str::contains($exception->getMessage(), 'errno: 150')){
                    $this->table = $this->renderTableName($exception->getMessage());
                    $this->getMigrations();
                } else {
                    return "There was an error: {$exception->getMessage()}";
                }
            }
        }

        krsort($this->migrations);

        return $this->migrations;
    }

    public function execute()
    {
        $migrations = $this->getMigrations();

        \Artisan::call('db:wipe', [
            '--force' => true
        ]);

        $message = "Start safe migrate: \n";
        foreach ($migrations as $migration) {
            try {
                \Artisan::call('migrate', [
                    '--path' => $migration,
                    '--realpath' => true,
                ]);
                $message .= \Artisan::output();
            } catch (\Exception $exception){
                return "There is an error: {$exception->getMessage()}";
            }
        }

        \Artisan::call('migrate');

        $message .= \Artisan::output();

        return $message;
    }

}
