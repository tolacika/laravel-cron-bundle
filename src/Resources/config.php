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
    |  Log driver for cron outputs
    |--------------------------------------------------------------------------
    |
    | Log drivers may be none, database, laravelLog or file
    |
     */
    'defaultCronOutput' => 'database',

    'cronOutputs' => [
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
        | With this option the package will log all the outputs to database,
        | You can truncate the output
        |
         */
        'database' => [
            'truncate' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        |  Log driver: laravelLog
        |--------------------------------------------------------------------------
        |
        | With this option the package will log all the changes to laravel.log,
        | Prefixes can be set in `prefix`
        |
        | log format placeholders:
        |  - prefix
        |  - datetime: format: Y-m-d H:i:s
        |  - userId: Given user id by CronBundle::setUser()
        |  - jobId
        |  - jobName
        |  - runTime
        |  - exitCode
        |  - output
        |
        | You can truncate the output
        |
         */
        'laravelLog' => [
            'prefix'  => 'CronBundle',
            'logFormat' => '[%datetime%][%prefix%][userId:%userId%][jobId:%jobId%][jobName:%jobName%][runTime:%runTime%][exitCode:%exitCode%] Output: %output%',
            'truncate' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        |  Log driver: file
        |--------------------------------------------------------------------------
        |
        | With this option the package will log all the outputs to a file
        | You can truncate the output
        |
         */
        'file' => [
            'path' => storage_path('logs/cron-bundle/output.log'),
            'truncate' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        |  Log driver: singleFile
        |--------------------------------------------------------------------------
        |
        | With this option the package will log all the outputs to multiple files
        | You can truncate the output
        |
         */
        'singleFile' => [
            'dirPath' => storage_path('logs/cron-bundle/outputs/'),
            'fileFormat' => '%datetime%_%jobId%_%jobName%_output.log',
            'truncate' => false,
        ],
    ],


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
        | With this option the package will log all the changes to laravel.log,
        | which are defined in `actions` config.
        | Prefixes can be set in `prefix`
        |
        | log format placeholders:
        |  - prefix
        |  - datetime: format: Y-m-d H:i:s
        |  - userId: Given user id by CronBundle::setUser()
        |  - jobId
        |  - jobName
        |  - action: create, update or destroy
        |  - changes
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
