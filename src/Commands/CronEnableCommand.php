<?php

namespace Tolacika\CronBundle\Commands;

use Illuminate\Console\Command;
use Tolacika\CronBundle\Models\CronJob;

class CronEnableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron-bundle:enable'
    . ' {job : The job to enable (name or id)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable a cron job';

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
        $jobId = $this->input->getArgument('job');
        $job = CronJob::findById($jobId);

        if ($job == null) {
            $job = CronJob::getJobsByName($jobId)->first();
        }

        if($job == null) {
            throw new \InvalidArgumentException("Unknown job: " . $jobId);
        }

        if ($job->isEnabled()) {
            throw new \InvalidArgumentException("The job is already enabled!");
        }

        $this->output->writeln(sprintf('<info>You are about to enable "%s".</info>', $job->name));

        if (!$this->confirm("Enable this job?", false)) {
            $this->output->writeln("<error>Enabling aborted</error>");
            return 0;
        }

        $job->enabled = '1';
        $job->save();

        $this->output->writeln(sprintf('<info>Cron "%s" was enabled.</info>', $job->name));

        return 0;
    }
}
