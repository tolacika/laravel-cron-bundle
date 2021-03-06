<?php

namespace Tolacika\CronBundle\Models;

use Carbon\Carbon;
use Cron\Job\ShellJob;
use Cron\Report\ReportInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Tolacika\CronBundle\Models\CronLog
 *
 * @property int $id
 * @property int $job_id
 * @property string $type
 * @property string $modified
 * @property int|null $user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Tolacika\CronBundle\Models\CronJob $job
 * @mixin \Eloquent
 */
class CronLog extends Model
{
    protected $table = 'cron_logs';

    /**
     * Returns the related CronJob
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function job()
    {
        return $this->belongsTo(CronJob::class, 'job_id', 'id');
    }

    /**
     * Generates the template for the modifies logs
     *
     * @return string
     * @throws \Throwable
     */
    public function formatModifies()
    {
        if ($this->type == 'destroy') {
            return 'Job deleted.';
        }

        $changes = json_decode($this->modified, true);

        return view('cron-bundle::changes', ['changes' => $changes])->render();
    }
}
