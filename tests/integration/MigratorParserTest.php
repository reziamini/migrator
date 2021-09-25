<?php

namespace MigratorTest\integration;

use MigratorTest\TestCase;
use Migrator\Service\MigratorParser;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Application;

class MigratorParserTest extends TestCase
{
    /** @test * */
    public function connection_will_be_returned_default_value(){
        $app = new Application;
        $app->useDatabasePath(__DIR__.'/../Dependencies/database');

        Config::shouldReceive('get')
            ->with('database.default')
            ->once()
            ->andReturn('test_database');

        $parser = new MigratorParser('user.php');
        $this->assertEquals($parser->getConnectionName(), 'test_database');
    }
}
