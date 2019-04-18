<?php

namespace Tolacika\CronBundle\Models;

use Carbon\Carbon;
use Cron\Job\ShellJob;
use Cron\Report\ReportInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Tolacika\CronBundle\Models\CronReport
 *
 * @property int $id
 * @property int $job_id
 * @property string $run_at
 * @property float $run_time
 * @property int $exit_code
 * @property string $output
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Tolacika\CronBundle\Models\CronJob $job
 * @mixin \Eloquent
 */
class CronReport extends Model
{
    protected $table = 'cron_reports';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function job()
    {
        return $this->belongsTo(CronJob::class, 'job_id', 'id');
    }

    /**
     * @param ReportInterface[] $reports
     */
    public static function saveReports(array $reports)
    {
        foreach ($reports as $report) {
            /** @var \Cron\Report\JobReport $report */
            /** @var ShellJob $job */
            $job = $report->getJob();
            /** @var CronJob $raw */
            $raw = $job->raw;
            $rep = new CronReport();
            $rep->output = implode("\n", $report->getOutput());
            $rep->exit_code = $job->getProcess()->getExitCode();
            $rep->run_at = Carbon::createFromFormat('U.u', $report->getStartTime());
            $rep->run_time = $report->getEndTime() - $report->getStartTime();
            $raw->reports()->save($rep);
        }
    }
}
