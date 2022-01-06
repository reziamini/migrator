<?php


namespace MigratorTest\unit;


use Migrator\Service\MigratorParser;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Carbon;

class MigratorParserTest extends TestCase
{

    /** @test * */
    public function name_will_be_parsed_successfully(){
        $parser = new MigratorParser('2014_10_12_000000_create_users_table');

        $this->assertEquals($parser->getName(), 'Create users table');
    }

    /** @test * */
    public function time_will_be_parsed(){
        $parser = new MigratorParser('2021_09_15_000000_create_users_table');

        Carbon::setTestNow(Carbon::create(2021, 9, 15, 1));
        $this->assertEquals($parser->getDate(), "1 hour ago");

        Carbon::setTestNow(Carbon::create(2021, 10, 15));
        $this->assertEquals($parser->getDate(), "1 month ago");

        Carbon::setTestNow(Carbon::create(2022, 9, 15));
        $this->assertEquals($parser->getDate(), "1 year ago");
    }
}
