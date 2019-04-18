<?php

namespace Tolacika\CronBundle\Commands;

use Illuminate\Console\Command;

class CronStopCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron-bundle:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stops cron scheduler';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pidFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . CronStartCommand::PID_FILE;
        if (!file_exists($pidFile)) {
            return 0;
        }
        if (!extension_loaded('pcntl')) {
            throw new \RuntimeException('This command needs the pcntl extension to run.');
        }
        if (!posix_kill(file_get_contents($pidFile), SIGINT)) {
            if (!unlink($pidFile)) {
                throw new \RuntimeException('Unable to stop scheduler.');
            }
            $this->output->writeln(sprintf('<comment>%s</comment>', 'Unable to kill cron scheduler process. Scheduler will be stopped before the next run.'));

            return 0;
        }
        unlink($pidFile);
        $this->output->writeln(sprintf('<info>%s</info>', 'Cron scheduler is stopped.'));
    }
}
