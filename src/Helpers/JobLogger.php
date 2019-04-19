<?php


namespace Tolacika\CronBundle\Helpers;


use Tolacika\CronBundle\Models\CronJob;

class JobLogger
{
    /**
     * @param $action
     * @param CronJob $job
     */
    public static function createLogEntry($action, CronJob $job)
    {
        $availableModes = array_keys(config('cron-bundle.logTypes'));
        $logMode = config('cron-bundle.log');
        if (!in_array($logMode, $availableModes)) {
            throw new \InvalidArgumentException("There are no such logging mode: " . $logMode);
        }

        $logConfig = config('cron-bundle.logTypes.' . $logMode);

        switch ($logMode) {
            case 'none':
                break;
            case 'database':
                self::createDatabaseLogEntry($action, $job, $logConfig);
                break;
            case 'laravelLog':
                self::createLaravelLogEntry($action, $job, $logConfig);
                break;
            case 'file':
                self::createFileLogEntry($action, $job, $logConfig);
                break;
        }
    }

    private static function createDatabaseLogEntry($action, CronJob $job, $logConfig)
    {
        throw new \Exception("Not implemented yet.");
    }

    private static function createLaravelLogEntry($action, CronJob $job, $logConfig)
    {
        throw new \Exception("Not implemented yet.");
    }

    private static function createFileLogEntry($action, CronJob $job, $logConfig)
    {
        throw new \Exception("Not implemented yet.");
    }
}
