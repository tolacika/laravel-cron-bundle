# Laravel Cron Bundle

## Introduction

With this package you can replace the default crontable. Or the Laravel's scheduling feature. This package allows you to define your crons in a database table and manage these from command or from an Admin panel. And you can run it from supervisor.

## Tested with Laravel versions

- Laravel 5.6

If you have problems with other versions please submit an issue.

## Installion

Use composer to install CronBundle packages:
```bash
composer require Tolacika/Laravel-Cron-Bundle
```

Once `CronBundle` is installed, run the migrations
```bash
php artisan migrate
```
And in case of need you can pubish the config file with this command:
```bash
php artisan vendor:publish --provider="Tolacika\CronBundle\CronBundleServiceProvider"
```

## Running Crons

### With crontab

```text
* * * * * php /path-to-your-project/artisan cron-bundle:execute >> /dev/null
```

### With supervisor

```ini
[program:cron_bundle]
command=php artisan cron:start --blocking
process_name=%(program_name)s_%(process_num)02d
numprocs=1
autostart=true
autorestart=true
startsecs=0
user=www-data
redirect_stderr=true
stderr_logfile = /var/log/supervisor/cron_bundle_err.log
stdout_logfile = /var/log/supervisor/cron_bundle_stdout.log
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=10
```

### Run as daemon

If you have `PCNTL` PHP extension you can start a daemon worker with this command:
```bash
php artisan cron-bundle:start
```

## Managing crons

### From command line

You have several interactive commands to use for creating, deleting, enabling and disabling crons:
- `cron-bundle:list` - to list all job
- `cron-bundle:create` - to create a job
- `cron-bundle:delete {jobId}` - to delete a job
- `cron-bundle:enable {jobId}` - to enable a job
- `cron-bundle:disable {jobId}` - to disable a job

### From dashboard

Also you have a fancy dashboard to manage your jobs. And from here you can see the run results and change logs.

![Jobs list](/src/Resources/Images/cron-bundle-dashboard.png)

![Job edit page](/src/Resources/Images/cron-bundle-edit.png)

## Dashboard

### Authenticating to dashboard

After package installed and migration is done, you can access the dashboard in `example.com/CronBundle`

By default the dashboard only available in local environment. To change this behaviour place this snippet to your `AppServiceProvider`'s `boot()` method, of course replace the `auth()->check()`:
```php
use Tolacika\CronBundle\CronBundle;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    // ...
    CronBundle::auth(function ($request) {
        // returns true or false
        return auth()->check();
    });
    // ...
}
```

### Identifying user for logs

If you are using log type other than `none`, its useful to set the current user for logging. You can do this with `CronBundle::setUser()` method in your `AppServiceProvider`'s `boot()` method, like this:

```php
use Tolacika\CronBundle\CronBundle;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    // ...
    CronBundle::setUser(function ($request) {
        // the return type must be an integer, or null
        return auth()->user() ? auth()->user()->id : null;
    });
    // ...
}
```

## Configuration

After publishing the config file, you can see some options in `config/cron-bundle.php`. Each config entry is documented here.

## Changelog

Important versions listed below. Refer to the [Changelog](CHANGELOG.md) for a full history of the project.

## Credits

- [Laszlo Toth](https://github.com/tolacika)

Bug reports, feature requests, and pull requests can be submitted by following our [Contribution Guide](CONTRIBUTING.md).

## Contributing & Protocols

- [Versioning](CONTRIBUTING.md#versioning)
- [Coding Standards](CONTRIBUTING.md#coding-standards)
- [Pull Requests](CONTRIBUTING.md#pull-requests)

## License

This software is released under the [MIT](LICENSE) License.

 © 2017 Laszlo Toth, All rights reserved. 
