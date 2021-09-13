<?php


namespace MigratorTest\unit;


use Migrator\Service\MigratorParser;
use PHPUnit\Framework\TestCase;

class MigratorParserTest extends TestCase
{

    /** @test * */
    public function name_will_be_parsed_successfully(){
        $parser = new MigratorParser('2014_10_12_000000_create_users_table');

        $this->assertEquals($parser->getName(), 'Create users table');
    }

}
