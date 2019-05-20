<?php


namespace Tolacika\CronBundle\Helpers;


use Carbon\Carbon;
use Tolacika\CronBundle\CronBundle;
use Tolacika\CronBundle\Models\CronJob;
use Tolacika\CronBundle\Models\CronLog;

class JobLogger
{
    /**
     * Universally creates a log entry based by config
     *
     * @param $action
     * @param CronJob $job
     */
    public static function createLogEntry($action, CronJob $job)
    {
        $availableModes = array_keys(config('cron-bundle.changeLogDrivers'));
        $logMode = config('cron-bundle.defaultChangeLog');
        if (!in_array($logMode, $availableModes)) {
            throw new \InvalidArgumentException("There are no such logging mode: " . $logMode);
        }

        $logConfig = config('cron-bundle.changeLogDrivers.' . $logMode);

        switch ($logMode) {
            case 'none':
                break;
            case 'database':
                self::createDatabaseLogEntry($action, $job, $logConfig);
                break;
            case 'laravelLog':
                self::createLaravelLogEntry($action, $job, $logConfig);
                break;
        }
    }

    /**
     * Creates a database entry based by config
     *
     * @param $action
     * @param CronJob $job
     * @param $logConfig
     */
    private static function createDatabaseLogEntry($action, CronJob $job, $logConfig)
    {
        if (!in_array($action, $logConfig['actions'])) {
            return;
        }

        $log = new CronLog();
        $log->type = $action;
        $log->modified = json_encode(self::getChanges($job));
        $log->user_id = CronBundle::getUser();
        $job->logs()->save($log);
    }

    /**
     * Creates a Laravel log entry based by cofig
     *
     * Todo: Implement
     *
     * @param $action
     * @param CronJob $job
     * @param $logConfig
     */
    private static function createLaravelLogEntry($action, CronJob $job, $logConfig)
    {
        if (!in_array($action, $logConfig['actions'])) {
            return;
        }

        $changes = [];

        foreach (self::getChanges($job) as $field => $change) {
            $changes[] = "The '$field' field modified from '{$change['old']}' to '{$change['new']}'";
        }

        $logPH = [
            '%prefix%' => $logConfig['prefix'] ?? "CronBundle",
            '%datetime%'=> now()->format('Y-m-d H:i:s'),
            '%userId%' => CronBundle::getUser(),
            '%jobId%' => $job->id,
            '%jobName%' => $job->name,
            '%action%' => $action,
            '%changes%' => implode('; ', $changes),
        ];

        $logEntry = $logConfig['logFormat'];

        $logEntry = strtr($logEntry, $logPH);

        \Log::info($logEntry);
    }

    /**
     * Returns the changes of a Job
     *
     * @param CronJob $job
     * @return array
     */
    private static function getChanges(CronJob $job)
    {
        $changes = $job->getDirty();
        $oldNew = [];

        foreach ($changes as $attr => $change) {
            $oldNew[$attr] = [
                'old' => $job->getOriginal($attr),
                'new' => self::formatAttribute($job, $attr),
            ];
        }

        return $oldNew;
    }

    /**
     * Formats attribute
     *
     * Needs to convert Carbon to string
     *
     * @param CronJob $job
     * @param $attr
     * @return mixed|string
     */
    private static function formatAttribute(CronJob $job, $attr)
    {
        $val = $job->getAttribute($attr);

        if ($val instanceof Carbon) {
            return $val->format('Y-m-d H:i:s');
        }

        return $val;
    }
}
