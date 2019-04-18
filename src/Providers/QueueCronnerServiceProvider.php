<?php

namespace Tolacika\CronBundle\Providers;

use Illuminate\Support\ServiceProvider;
use Tolacika\CronBundle\Commands\CronCreateCommand;
use Tolacika\CronBundle\Commands\CronDeleteCommand;
use Tolacika\CronBundle\Commands\CronDisableCommand;
use Tolacika\CronBundle\Commands\CronEnableCommand;
use Tolacika\CronBundle\Commands\CronExecutorCommand;
use Tolacika\CronBundle\Commands\CronListCommand;
use Tolacika\CronBundle\Commands\CronStartCommand;
use Tolacika\CronBundle\Commands\CronStopCommand;

class QueueCronnerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //$this->loadRoutesFrom(__DIR__ . "/../routes.php");
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');

        $this->commands([
            CronExecutorCommand::class,
            CronStartCommand::class,
            CronStopCommand::class,
            CronListCommand::class,
            CronCreateCommand::class,
            CronDeleteCommand::class,
            CronEnableCommand::class,
            CronDisableCommand::class,
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
