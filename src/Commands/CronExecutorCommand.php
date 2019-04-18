<?php

namespace Tolacika\CronBundle\Commands;

use Cron\Cron;
use Cron\Executor\Executor;
use Illuminate\Console\Command;
use Tolacika\CronBundle\Cron\Resolver;
use Tolacika\CronBundle\Models\CronReport;

class CronExecutorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron-bundle:execute'
    . ' {--j|job= : Run only this job (if exists and enabled)}'
    . ' {--f|force : Force the current job (even if disabled) only available with job option}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs any currently schedule cron jobs';

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
        $cron = new Cron();

        $cron->setExecutor(new Executor());

        $resolver = new Resolver();
        if ($this->hasOption('job')) {
            $resolver->setJobName($this->option('job'));
        }
        if ($this->hasOption('force')) {
            $resolver->setForce($this->option('force'));
        }

        $cron->setResolver($resolver);

        $startTime = microtime(true);

        /** @var \Cron\Report\CronReport $dbReport */
        $dbReport = $cron->run();

        while ($cron->isRunning()) {
            usleep(1e4);
        }

        $this->output->writeln("\nCron time: " . (microtime(true) - $startTime) . " s");
        $this->output->writeln("Crons executed:");
        foreach ($dbReport->getReports() as $report) {
            $this->output->writeln("  " . $report->getJob()->raw->name);
        }

        CronReport::saveReports($dbReport->getReports());

        return 0;
    }
}
