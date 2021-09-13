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

}
