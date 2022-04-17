<?php


namespace Migrator\Service;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

/**
 * Class SafeMigrate
 * This class is used to apply safe migration while resolving the
 * correct order of migrations. Using this migration approach will
 * handle all foreign key control failures in unordered migrations.
 *
 * ! Warning: Note that on running safe migrate, it will first wipe the database.
 *
 * @package Migrator\Service
 */
class SafeMigrate
{
    /**
     * @var string Table Name
     */
    protected $table;

    /**
     * @var array Migrations list
     */
    protected $migrations = [];

    /**
     * SafeMigrate constructor.
     * @param $error
     */
    public function __construct($error)
    {
        $this->table = $this->renderTableName($error);
    }

    /**
     * Extract table name from SQL foreign key constraint error.
     *
     * @param $error
     * @return mixed|string
     */
    public function renderTableName($error)
    {
        preg_match("/.*references `(\w+)`.*/", $error, $match);

        return $match[1] ?? '';
    }

    /**
     * Get the list of migration files with the target table name from the migrations directory.
     *
     * @return array
     */
    public function getMigrationFiles()
    {
        $migrations = File::glob(database_path("migrations".DIRECTORY_SEPARATOR."*{$this->table}*"));

        return [$this->table => $migrations];
    }

    /**
     * Get the safe-to-run list of migrations.
     * ! Warning: This function will wipe the database. Be careful on usage.
     *
     * This will run run and sort
     * @return array|string
     */
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

    /**
     * Run the ordered migrations of target table.
     * We will also call the normal migrate since we have previously wiped the database.
     *
     * @return array
     */
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

    /**
     * Run migrations.
     *
     * @param string $path
     * @return string
     */
    private function runMigration($path){
        \Artisan::call('migrate', [
            '--path' => $path,
            '--realpath' => true,
        ]);

        return Artisan::output();
    }

    /**
     * Clean the database. Drop all tables, views, and types.
     */
    private function clearDatabase()
    {
        \Artisan::call('db:wipe', [
            '--force' => true
        ]);
    }

}
