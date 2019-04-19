<?php

return [

    'dashboardMiddleware' => [
        'web',
    ],

    'needExtraAuth' => true,

    'commandFilter' => [
        'inspire',
        'up',
        'down',
        'cron-bundle:*',
    ],

    'filterType' => 'whitelist',

    'log' => 'database',

    'logTypes' => [
        'none' => [],

        'database' => [
            'actions' => [
                'create',
                'update',
                'destroy',
            ],
        ],

        'laravelLog' => [
            'prefix'  => 'CronBundle',
            'actions' => [
                'create',
                'update',
                'destroy',
            ],
        ],

        'file' => [
            'path'    => storage_path('logs/cron-bundle.log'),
            'actions' => [
                'create',
                'update',
                'destroy',
            ],
        ],
    ],
];
