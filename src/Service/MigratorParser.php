<?php


namespace Migrator\Service;


use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class MigratorParser
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        $name = $this->name;

        preg_match('/\d+_\d+_\d+_\d+_(\w+)/', $name, $m);

        $name = Str::ucfirst(Str::replace('_', ' ', $m[1]));

        return $name;
    }

    public function getDate()
    {
        $date = $this->name;

        preg_match('/([\d+\_]+)/', $date, $m);

        $date = Str::replace('_', ' ', $m[1]);

        return Carbon::createFromFormat('Y m d His ', $date)->ago();
    }

    public function getConnectionName()
    {
        $file = database_path('migrations'.DIRECTORY_SEPARATOR.$this->name);
        $migrationObject = (function () use ($file) {
            return $this->resolvePath($file);
        })->call(app('migrator'));

        return $migrationObject->getConnection() ?: config('database.default');
    }

    public function getStructure()
    {
        $file = database_path('migrations'.DIRECTORY_SEPARATOR.$this->name);

        $contents = file_get_contents($file);

        $searchForOne = '$table->';

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
