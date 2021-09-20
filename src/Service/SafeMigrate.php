<?php


namespace Migrator\Service;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SafeMigrate
{
    protected $table;

    public function __construct($error)
    {
        $this->table = $this->renderTableName($error);
    }

    public function renderTableName($error)
    {
        preg_match("/.*references `(\w+)`.*/", $error, $match);

        return $match[1];
    }

    public function getMigrations()
    {
        $migrations = File::glob(database_path("migrations/*{$this->table}*"));

        return $migrations;
    }

    public function execute()
    {
        $migrations = $this->getMigrations();

        if (is_null($migrations)){
            return "No dependency founds for `{$this->table}` table";
        }

        \Artisan::call('db:wipe', [
            '--force' => true
        ]);

        $output = "Start safe migration : \n";

        foreach ($migrations as $migration) {
            try {
                \Artisan::call('migrate', [
                    '--path' => $migration,
                    '--realpath' => true,
                ]);
            } catch (\Exception $exception){
                if (\Str::contains($exception->getMessage(), 'errno: 150')){
                    $this->table = $this->renderTableName($exception->getMessage());
                    Log::alert($exception->getMessage());
                    $this->execute();
                } else {
                    return "There was an error: {$exception->getMessage()}";
                }
            }
        }

        \Artisan::call('migrate');

        return $output.\Artisan::output();
    }

}
