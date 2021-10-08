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
        $file = database_path('migrations\\'.$this->name);

        $contents = file_get_contents($file);

        $searchForOne = '$connection';
        $searchForTwo = 'Schema::connection';

        $patternOne = preg_quote($searchForOne, '/');
        $patternOne = "/^.*$patternOne.*\$/m";

        $patternTwo = preg_quote($searchForTwo, '/');
        $patternTwo = "/^.*$patternTwo.*\$/m";

        if (preg_match($patternOne, $contents, $matches)){
            $match = trim(implode("\n", $matches));
            $match = str_replace('"', "'", $match);

            return substr($match, stripos($match, "'") + 1, (strripos($match, "'") - stripos($match, "'")) - 1);
        }

        if(preg_match($patternTwo, $contents, $matches)){
            $match = trim(implode("\n", $matches));
            preg_match('/Schema::connection\(["|\'](.*)["|\']\)/', $match, $m);

            return $m[1];
        }

        return \Config::get('database.default');
    }

    public function getStructure()
    {
        $file = database_path('migrations\\'.$this->name);

        $contents = file_get_contents($file);

        $searchForOne = '$table->';

        $patternOne = preg_quote($searchForOne, '/');
        
        $patternOne = "/^.*$patternOne.*\$/m";
        
        preg_match_all($patternOne, $contents, $matches);
        
        $structure = new StructureParser($matches);
        
        return $structure->getStructure();
    }
}
