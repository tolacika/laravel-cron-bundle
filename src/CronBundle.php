<?php


namespace Tolacika\CronBundle;


use Closure;
use Symfony\Component\Console\Command\Command;

class CronBundle
{
    /**
     * The callback that should be used to authenticate users.
     *
     * @var \Closure
     */
    private static $authUsing;

    /**
     * The callback that should be used to identify user for log.
     *
     * @var \Closure
     */
    private static $authUser;

    /**
     * Determine if the given request can access the dashboard.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public static function check($request)
    {
        return (static::$authUsing ?: function () {
            return app()->environment('local');
        })($request);
    }

    /**
     * Set the callback that should be used to authenticate users.
     *
     * @param \Closure $callback
     */
    public static function auth(Closure $callback)
    {
        static::$authUsing = $callback;
    }

    /**
     * Returns the identified user id
     *
     * @return mixed
     */
    public static function getUser()
    {
        return (static::$authUser ?: function () {
            return null;
        })();
    }

    /**
     * Set the callback that should be used to identify user for log
     *
     * @param Closure $callback
     */
    public static function setUser(Closure $callback)
    {
        static::$authUser = $callback;
    }

    /**
     * @return array
     */
    public static function getPredefinedSchedules()
    {
        return [
            "* * * * * *"    => "Every minute",
            "*/5 * * * * *"  => "Every 5 minute",
            "*/10 * * * * *" => "Every 10 hour",
            "*/30 * * * * *" => "Every half hour",
            "0 * * * * *"    => "Every hour",
            "0 0 * * * *"    => "Every day at 12 pm",
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function getAvailableCommands()
    {
        $commandFilter = config('cron-bundle.commandFilter');
        $isWhiteList = config('cron-bundle.filterType') != 'blacklist';
        $allCommands = collect(\Artisan::all());

        if (!empty($commandFilter)) {
            $allCommands = $allCommands->filter(function (Command $command) use ($commandFilter, $isWhiteList) {
                foreach ($commandFilter as $filter) {
                    if (fnmatch($filter, $command->getName())) {
                        return $isWhiteList;
                    }
                }

                return !$isWhiteList;
            });
        }

        $allCommands = $allCommands->map(function (Command $command) {
            return $command->getName();
        })->sortBy(function ($name) {
            if (mb_strpos($name, ':') === false) {
                $name = ':' . $name;
            }

            return $name;
        });

        return $allCommands;
    }

    /**
     * @param $command
     * @return bool
     */
    public static function isCommandAllowed($command)
    {
        return self::getAvailableCommands()->has($command);
    }
}
