<?php

return [

    /**
     *  _____                ______                 _ _        _____              __ _
     * /  __ \               | ___ \               | | |      /  __ \            / _(_)
     * | /  \/_ __ ___  _ __ | |_/ /_   _ _ __   __| | | ___  | /  \/ ___  _ __ | |_ _  __ _
     * | |   | '__/ _ \| '_ \| ___ \ | | | '_ \ / _` | |/ _ \ | |    / _ \| '_ \|  _| |/ _` |
     * | \__/\ | | (_) | | | | |_/ / |_| | | | | (_| | |  __/ | \__/\ (_) | | | | | | | (_| |
     *  \____/_|  \___/|_| |_\____/ \__,_|_| |_|\__,_|_|\___|  \____/\___/|_| |_|_| |_|\__, |
     *                                                                                  __/ |
     *                                                                                 |___/
     */

    /*
    |--------------------------------------------------------------------------
    |  Dashboard's middleware
    |--------------------------------------------------------------------------
    |
    | Defines the middleware for the Dashboard controller
    | You can use multiple middlewares
    |
     */
    'dashboardMiddleware' => [
        'web',
    ],

    /*
    |--------------------------------------------------------------------------
    |  Extra auth for Dashboard
    |--------------------------------------------------------------------------
    |
    | Defines if the dashboard needs extra auth
    | (for example permission check or guards)
    |
    | Usage:
    | Put this code to your AppServiceProvider
    | <code>
    |     CronBundle::auth(function ($request) {
    |         return PermCheck::admin();
    |     });
    | </code>
     */
    'needExtraAuth' => true,

    /*
    |--------------------------------------------------------------------------
    |  Command filter black or whitelist
    |--------------------------------------------------------------------------
    |
    | Defines the commands what you can use in the CronJobs
    | Supports ? and * wildcards
    |
     */
    'commandFilter' => [
        'inspire',
        'up',
        'down',
        'cron-bundle:*',
    ],

    /*
    |--------------------------------------------------------------------------
    |  Filter type
    |--------------------------------------------------------------------------
    |
    | Defines that the command filter is `whitelist` or `blacklist`
    | Whitelist recommended
    |
     */
    'filterType' => 'whitelist',

    /*
    |--------------------------------------------------------------------------
    |  Log driver for changes
    |--------------------------------------------------------------------------
    |
    | Log drivers may be none, database, laravelLog or file
    |
     */
    'defaultChangeLog' => 'database',

    'changeLogDrivers' => [
        /*
        |--------------------------------------------------------------------------
        |  Log driver: none
        |--------------------------------------------------------------------------
        |
        | With this option the package will not log any information
        |
         */
        'none' => [],

        /*
        |--------------------------------------------------------------------------
        |  Log driver: database
        |--------------------------------------------------------------------------
        |
        | With this option the package will log all the changes to database,
        | which are defined in `actions` config
        |
         */
        'database' => [
            'actions' => [
                'create',
                'update',
                'destroy',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        |  Log driver: laravelLog
        |--------------------------------------------------------------------------
        |
        | Todo:implement
        |
        | With this option the package will log all the changes to laravel.log,
        | which are defined in `actions` config.
        | Prefixes can be set in `prefix`
        |
         */
        'laravelLog' => [
            'prefix'  => 'CronBundle',
            'logFormat' => '[%datetime%][%prefix%][userId:%userId%][jobId:%jobId%][jobName:%jobName%][action:%action%] Changes: %changes%',
            'actions' => [
                'create',
                'update',
                'destroy',
            ],
        ],
    ],
];
