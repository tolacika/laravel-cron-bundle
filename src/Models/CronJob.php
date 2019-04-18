<?php

namespace Tolacika\CronBundle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Tolacika\CronBundle\Models\CronJob
 *
 * @property int $id
 * @property string $name
 * @property string $command
 * @property string $schedule
 * @property string $description
 * @property bool $enabled
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Tolacika\CronBundle\Models\CronReport[] $reports
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\Tolacika\CronBundle\Models\CronJob onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\Tolacika\CronBundle\Models\CronJob withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Tolacika\CronBundle\Models\CronJob withoutTrashed()
 * @mixin \Eloquent
 */
class CronJob extends Model
{
    use SoftDeletes;

    protected $table = 'cron_jobs';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(CronReport::class, 'job_id', 'id');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled == '1';
    }

    /**
     * Returns all enabled jobs
     *
     * @return \Illuminate\Database\Eloquent\Collection|CronJob[]
     */
    public static function getEnabledJobs()
    {
        return static::where('enabled', '1')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|CronJob[]
     */
    public static function getAllJobs()
    {
        return static::all();
    }

    /**
     * @param string|null $jobName
     * @param bool $force
     * @return \Illuminate\Database\Eloquent\Collection|CronJob[]
     */
    public static function getJobsByName(?string $jobName, bool $force = false)
    {
        $builder = static::where('name', $jobName);
        if (!$force) {
            $builder = $builder->where('enabled', '1');
        }

        return $builder->get();
    }

    /**
     * @param $jobId
     * @return \Illuminate\Database\Eloquent\Collection|Model|CronJob|CronJob[]|null
     */
    public static function findById($jobId)
    {
        return self::find($jobId);
    }
}
