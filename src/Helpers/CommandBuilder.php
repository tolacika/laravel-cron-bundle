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

    /**
     * Build the command string for cron worker
     *
     * @param string $command
     * @return string
     */
    public static function build(string $command)
    {
        if (self::$phpExecutable == null) {
            $finder = new PhpExecutableFinder();
            self::$phpExecutable = $finder->find();
        }

        return sprintf("%s %s %s", self::$phpExecutable, $_SERVER['SCRIPT_NAME'], $command);
    }

    /**
     * Returns the artisan command's script name
     *
     * Todo: Refactor needed
     *
     * @return mixed
     */
    public static function getScriptName()
    {
        return $_SERVER['SCRIPT_NAME'];
    }
}
