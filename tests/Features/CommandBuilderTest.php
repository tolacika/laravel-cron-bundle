<?php


namespace Features;


use Symfony\Component\Process\PhpExecutableFinder;
use Tolacika\CronBundle\Helpers\CommandBuilder;
use Tolacika\CronBundle\Tests\TestCase;

class CommandBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function test_command_builder()
    {
        $executable = (new PhpExecutableFinder())->find();
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $command = $executable . " " . $scriptName . " mocked --force";

        $this->assertEquals($command, CommandBuilder::build("mocked --force"));
    }
}
