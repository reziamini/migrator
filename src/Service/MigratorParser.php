<?php


namespace Migrator\Service;


use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class MigratorParser.
 * This class takes a migration file as input and parses its content.
 * @package Migrator\Service
 */
class MigratorParser
{
    /**
     * @var string Migration Name
     */
    public $name;

    /**
     * @var SplFileInfo Migration File
     */
    public $migration;

    /**
     * MigratorParser constructor.
     * @param $migration
     */
    public function __construct($migration)
    {
        $this->name = $migration->getFilename();
        $this->migration = $migration;
    }

    /**
     * Get a human-readable name from the migration name.
     * i.e. If the migration name is '2014_10_12_000000_create_users_table'
     * it will return 'Create users table'.
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->name;

        preg_match('/\d+_\d+_\d+_\d+_(\w+)/', $name, $m);

        $name = Str::ucfirst(Str::replace('_', ' ', $m[1]));

        return $name;
    }

    /**
     * Get the migration creation date difference from today in a human-readable format.
     *
     * @return string
     */
    public function getDate()
    {
        $date = $this->name;

        preg_match('/([\d+\_]+)/', $date, $m);

        $date = Str::replace('_', ' ', $m[1]);

        return Carbon::createFromFormat('Y m d His ', $date)->ago();
    }

    /**
     * Get the migration connection name.
     *
     * @return string
     */
    public function getConnectionName()
    {
        $file = $this->migration->getPathname();
        $migrationObject = (function () use ($file) {
            return $this->resolvePath($file);
        })->call(app('migrator'));

        return $migrationObject->getConnection() ?: config('database.default');
    }

    /**
     * Get structure content of migration.
     *
     * @return array
     */
    public function getStructure()
    {
        $contents = $this->migration->getContents();

        preg_match('/Schema::.+(?:\n+)?function\s?\(.*?\$(\w+)/mi', $contents, $m);

        $tableName = $m[1] ?? 'table';

        $searchForOne = '$'.$tableName.'->';

        $patternOne = preg_quote($searchForOne, '/');

        $patternOne = "/^.*$patternOne.*\$/m";

        preg_match_all($patternOne, $contents, $matches);

        try {
            $structure = new StructureParser($matches);

            return $structure->getStructure();
        } catch (\Exception $exception){
            return [];
        }

    }
}
