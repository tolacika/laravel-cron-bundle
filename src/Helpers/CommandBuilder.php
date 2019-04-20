<?php


namespace Tolacika\CronBundle\Helpers;


use Symfony\Component\Process\PhpExecutableFinder;

class CommandBuilder
{

    private static $phpExecutable = null;

    /**
     * CommandBuilders constructor.
     */
    private function __construct() { }

    public static function build(string $command)
    {
        if (self::$phpExecutable == null) {
            $finder = new PhpExecutableFinder();
            self::$phpExecutable = $finder->find();
        }

        return sprintf("%s %s %s", self::$phpExecutable, $_SERVER['SCRIPT_NAME'], $command);
    }

    public static function getScriptName()
    {
        return $_SERVER['SCRIPT_NAME'];
    }
}
