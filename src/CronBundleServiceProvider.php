<?php

namespace Tolacika\CronBundle;

use Illuminate\Support\ServiceProvider;
use Tolacika\CronBundle\Commands\CronCreateCommand;
use Tolacika\CronBundle\Commands\CronDeleteCommand;
use Tolacika\CronBundle\Commands\CronDisableCommand;
use Tolacika\CronBundle\Commands\CronEnableCommand;
use Tolacika\CronBundle\Commands\CronExecutorCommand;
use Tolacika\CronBundle\Commands\CronListCommand;
use Tolacika\CronBundle\Commands\CronStartCommand;
use Tolacika\CronBundle\Commands\CronStopCommand;
use Tolacika\CronBundle\Http\Controllers\CronBundleController;

class CronBundleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make(CronBundleController::class);
        $this->loadViewsFrom(__DIR__ . "/Resources/Views", 'cron-bundle');
        $this->loadRoutesFrom(__DIR__ . "/routes.php");

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

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

        $this->publishes([
            __DIR__ . "/Resources/config.php" => config_path("cron-bundle.php"),
        ], 'config');

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/Resources/config.php", 'cron-bundle');
    }
}
