<?php

namespace MigratorTest\integration;

use Migrator\Service\MigratorParser;
use MigratorTest\TestCase;

class MigratorParserTest extends TestCase
{
    /** @test * */
    public function connection_will_be_returned_default_value()
    {
        // arrange
        app()->useDatabasePath(__DIR__.'/../Dependencies/database');
        config()->set('database.default', 'example_connection');

        //act
        $parser = new MigratorParser('2014_11_32_53600_create_users_table.php');

        // assert
        $this->assertEquals($parser->getConnectionName(), 'example_connection');
    }

    /** @test * */
    public function migration_connection_will_be_parsed()
    {
        app()->useDatabasePath(__DIR__.'/../Dependencies/database');

        $parser = new MigratorParser('2014_11_32_53601_create_posts_table.php');
        $this->assertEquals($parser->getConnectionName(), 'custom_connection');

        $parser = new MigratorParser('2014_11_32_53602_update_posts_table.php');
        $this->assertEquals($parser->getConnectionName(), 'Hello_world');
    }
}
