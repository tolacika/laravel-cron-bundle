<?php

namespace Tolacika\CronBundle\Commands;

use Illuminate\Console\Command;
use Tolacika\CronBundle\Models\CronJob;

class CronDisableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron-bundle:disable'
    . ' {job : The job to disable (name or id)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable a cron job';

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
        // Finding job by id
        $job = CronJob::findById($jobId);

        if ($job == null) {
            // If not found try to find job by name
            $job = CronJob::getJobsByName($jobId)->first();
        }

        if($job == null) {
            // If not found throws Exception
            throw new \InvalidArgumentException("Unknown job: " . $jobId);
        }

        if (!$job->isEnabled()) {
            // If the job is disabled throws an exception
            throw new \InvalidArgumentException("The job is already disabled!");
        }

        $this->output->writeln(sprintf('<info>You are about to disable "%s".</info>', $job->name));

        // Disabling confirm
        if (!$this->confirm("Disable this job?", false)) {
            $this->output->writeln("<error>Disabling aborted</error>");
            return 0;
        }

        // Disabling the job
        $job->enabled = '0';
        $job->save();

        $this->output->writeln(sprintf('<info>Cron "%s" was disabled.</info>', $job->name));

        return 0;
    }
}
