<?php


namespace Tolacika\CronBundle\Cron;


use Cron\Job\JobInterface;
use Cron\Job\ShellJob;
use Cron\Resolver\ResolverInterface;
use Cron\Schedule\CrontabSchedule;
use Tolacika\CronBundle\Models\CronJob;

class Resolver implements ResolverInterface
{
    /**
     * @var string|null
     */
    private $jobName = null;
    /**
     * @var bool
     */
    private $force = false;

    /**
     * Return all available jobs.
     *
     * @return JobInterface[]
     */
    public function resolve()
    {
        if ($this->jobName !== null) {
            $jobs = CronJob::getJobsByName($this->jobName, $this->force);

            if ($jobs->isEmpty()) {
                throw new \InvalidArgumentException("Unknown job: " . $this->jobName);
            }
        } else {
            $jobs = CronJob::getEnabledJobs();
        }

        return array_map([$this, 'createJob'], $jobs->all());
    }

    /**
     * @param CronJob $cronJob
     * @return ShellJob
     */
    public function createJob(CronJob $cronJob)
    {
        $job = new ShellJob();
        $job->setCommand(CommandBuilder::build($cronJob->command), base_path());
        $job->setSchedule(new CrontabSchedule($cronJob->schedule));
        $job->raw = $cronJob;

        return $job;
    }

    /**
     * @param string|null $jobName
     */
    public function setJobName(?string $jobName): void
    {
        $this->jobName = $jobName;
    }

    /**
     * @param bool $force
     */
    public function setForce(bool $force): void
    {
        $this->force = $force;
    }
}
