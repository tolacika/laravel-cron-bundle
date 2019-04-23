<?php


namespace Tolacika\CronBundle\Helpers;


use Cron\Exception\InvalidPatternException;
use Cron\Validator\CrontabValidator;
use Tolacika\CronBundle\CronBundle;
use Tolacika\CronBundle\Models\CronJob;

class CronHelper
{
    /**
     * @param string $name
     * @param string $command
     * @param array $arguments
     * @param string $schedule
     * @param string $description
     * @param bool $enabled
     */
    public static function createCron($name, $command, $arguments = [], $schedule = "* * * * * *", $description = "", $enabled = true)
    {
        if (CronJob::getJobsByName($name, true)->isNotEmpty()) {
            // Invalid if already in use (Excepts deleted)
            throw new \InvalidArgumentException('Name already in use.');
        }

        if (!CronBundle::isCommandAllowed($command)) {
            throw new \InvalidArgumentException('Given command is not allowed');
        }

        if (!is_array($arguments)) {
            $arguments = [$arguments];
        }

        try {
            $validator = new CrontabValidator();
            // Invalid if CrontabValidator can't validate
            $validator->validate($schedule);
        } catch (InvalidPatternException $ex) {
            // Transform InvalidPatternException to InvalidArgumentException
            throw new \InvalidArgumentException("Invalid schedule pattern: " . $ex->getMessage());
        }

        $job = new \Tolacika\CronBundle\Models\CronJob();
        $job->name = $name;
        $job->command = $command . " " . implode(" ", $arguments);
        $job->schedule = $schedule;
        $job->description = $description ?? "";
        $job->enabled = $enabled ? "1" : "0";
        $job->save();
    }

    /**
     * @param $name
     */
    public static function enableCron($name)
    {
        /** @var CronJob $job */
        $job = CronJob::getJobsByName($name, true)->first();
        if ($job == null) {
            throw new \InvalidArgumentException('Command not found');
        }
        $job->enabled = "1";
        $job->save();
    }

    /**
     * @param $name
     */
    public static function disableCron($name)
    {
        /** @var CronJob $job */
        $job = CronJob::getJobsByName($name, true)->first();
        if ($job == null) {
            throw new \InvalidArgumentException('Command not found');
        }
        $job->enabled = "0";
        $job->save();
    }
}
