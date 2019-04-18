<?php

namespace Tolacika\CronBundle\Commands;

use Illuminate\Console\Command;
use Tolacika\CronBundle\Models\CronJob;

class CronListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron-bundle:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available crons';

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
        $jobs = CronJob::getAllJobs();

        $headers = [
            '#',
            'Name',
            'Command',
            'Schedule',
            'Enabled',
        ];

        $rows = [];

        foreach ($jobs as $job) {
            $rows[] = [
                $job->id,
                $job->name,
                $job->command,
                $job->schedule,
                $job->enabled == '1' ? 'Yes' : 'No',
            ];
        }

        $this->table($headers, $rows, 'box');

        return 0;
    }
}
