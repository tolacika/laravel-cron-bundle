<?php

namespace Tolacika\CronBundle\Commands;

use Illuminate\Console\Command;
use Tolacika\CronBundle\Models\CronJob;

class CronDeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron-bundle:delete'
    . ' {job : The job to delete (name or id)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a cron job';

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
            throw new \InvalidArgumentException("The job should be disabled first!");
        }

        $this->output->writeln(sprintf('<info>You are about to delete "%s".</info>', $job->name));

        if (!$this->confirm("Delete this job?", false)) {
            $this->output->writeln("<error>Deleting aborted</error>");
            return 0;
        }

        $job->delete();

        $this->output->writeln(sprintf('<info>Cron "%s" was deleted.</info>', $job->name));

        return 0;
    }
}
