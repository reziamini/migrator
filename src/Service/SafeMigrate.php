<?php


namespace Migrator\Service;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

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
        $migrations = File::glob(database_path("migrations".DIRECTORY_SEPARATOR."*{$this->table}*"));

        return [$this->table => $migrations];
    }

    public function getMigrations()
    {
        $migrations = $this->getMigrationFiles();
        $this->migrations[] = $migrations;

        if (is_null($migrations)){
            return "No dependency founded for `{$this->table}` table";
        }

        $this->clearDatabase();

        foreach ($migrations as $table => $migration) {
            if ($migration == []){
                continue;
            }

            try {
                $this->runMigration($migration);
            } catch (\Exception $exception){
                if (\Str::contains($exception->getMessage(), 'errno: 150')){
                    $this->table = $this->renderTableName($exception->getMessage());
                    $this->getMigrations();
                } else {
                    return "There was an error: {$exception->getMessage()}";
                }
            }
        }

        $this->clearDatabase();

        krsort($this->migrations);

        return $this->migrations;
    }

    public function execute()
    {
        $migrations = $this->getMigrations();

        $message = "Start safe migrate: \n";
        foreach ($migrations as $migration) {
            $pathArray = $migration[array_key_first($migration)];
            if ($pathArray == []){
                return [
                    'message' => "Dependencies for `".array_key_first($migration)."` table not found!",
                    'type' => 'error'
                ];
            }

            try {
                $message .= $this->runMigration($pathArray);
            } catch (\Exception $exception){
                return [
                    'message' => "There is an error: {$exception->getMessage()}",
                    'type' => 'error'
                ];
            }
        }

        \Artisan::call('migrate');

        $message .= \Artisan::output();

        return [
            'message' => $message,
            'type' => 'success'
        ];
    }

    private function runMigration($path){
        \Artisan::call('migrate', [
            '--path' => $path,
            '--realpath' => true,
        ]);

        return Artisan::output();
    }

    private function clearDatabase()
    {
        \Artisan::call('db:wipe', [
            '--force' => true
        ]);
    }

}
