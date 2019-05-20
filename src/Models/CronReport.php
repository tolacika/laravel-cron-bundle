<?php

namespace Tolacika\CronBundle\Models;

use Carbon\Carbon;
use Cron\Job\ShellJob;
use Cron\Report\JobReport;
use Cron\Report\ReportInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Tolacika\CronBundle\CronBundle;

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
     * Returns the related CronJob
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function job()
    {
        return $this->belongsTo(CronJob::class, 'job_id', 'id');
    }

    /**
     * Saves the reports from the CronExecutor
     *
     * @param ReportInterface[] $reports
     * @throws Exception
     */
    public static function saveReports(array $reports)
    {
        foreach ($reports as $report) {
            /** @var \Cron\Report\JobReport $report */


            $availableModes = array_keys(config('cron-bundle.cronOutputs'));
            $logMode = config('cron-bundle.defaultCronOutput');
            if (!in_array($logMode, $availableModes)) {
                throw new \InvalidArgumentException("There are no such logging mode: " . $logMode);
            }

            $logConfig = config('cron-bundle.cronOutputs.' . $logMode);

            switch ($logMode) {
                case 'none':
                    break;
                case 'database':
                    self::createDatabaseReport($report, $logConfig);
                    break;
                case 'laravelLog':
                    self::createLaravelLogReport($report, $logConfig);
                    break;
                case 'file':
                    self::createFileOutputReport($report, $logConfig);
                    break;
                case 'singleFile':
                    self::createSingleFileOutputReport($report, $logConfig);
                    break;
            }
        }
    }

    /**
     * @param JobReport $report
     * @param array $logConfig
     */
    private static function createDatabaseReport(JobReport $report, array $logConfig)
    {
        /** @var ShellJob $shellJob */
        $shellJob = $report->getJob();
        $jobOutput = implode("\n", $report->getOutput());
        if ($logConfig['truncate']) {
            $jobOutput = substr($jobOutput, 0, intval($logConfig['truncate']));
        }
        /** @var CronJob $cronJob */
        $cronJob = $shellJob->raw;

        $rep = new CronReport();
        $rep->output = $jobOutput;
        $rep->exit_code = $shellJob->getProcess()->getExitCode();
        $rep->run_at = Carbon::createFromFormat('U.u', $report->getStartTime());
        $rep->run_time = $report->getEndTime() - $report->getStartTime();
        $cronJob->reports()->save($rep);
    }

    /**
     * @param JobReport $report
     * @param array $logConfig
     */
    private static function createLaravelLogReport(JobReport $report, array $logConfig)
    {
        /** @var ShellJob $shellJob */
        $shellJob = $report->getJob();
        $jobOutput = implode("\n", $report->getOutput());
        if ($logConfig['truncate']) {
            $jobOutput = substr($jobOutput, 0, intval($logConfig['truncate']));
        }
        /** @var CronJob $cronJob */
        $cronJob = $shellJob->raw;

        $logPH = [
            '%prefix%'   => $logConfig['prefix'] ?? "CronBundle",
            '%datetime%' => now()->format('Y-m-d H:i:s'),
            '%userId%'   => CronBundle::getUser(),
            '%jobId%'    => $cronJob->id,
            '%jobName%'  => $cronJob->name,
            '%runTime%'  => $report->getEndTime() - $report->getStartTime(),
            '%exitCode%' => $shellJob->getProcess()->getExitCode(),
            '%output%'   => $jobOutput,
        ];

        $logEntry = $logConfig['logFormat'];

        $logEntry = strtr($logEntry, $logPH);

        \Log::info($logEntry);
    }

    /**
     * @param JobReport $report
     * @param array $logConfig
     * @throws Exception
     */
    private static function createFileOutputReport(JobReport $report, array $logConfig)
    {
        /** @var ShellJob $shellJob */
        $shellJob = $report->getJob();
        $jobOutput = implode("\n", $report->getOutput());
        if ($logConfig['truncate']) {
            $jobOutput = substr($jobOutput, 0, intval($logConfig['truncate']));
        }
        /** @var CronJob $cronJob */
        $cronJob = $shellJob->raw;

        $filename = $logConfig['path'];

        if (!file_exists($filename)) {
            $dirPath = dirname($filename);
            mkdir($dirPath, 0777, true);
            touch($filename);
        }

        $handler = fopen($filename, 'a');
        if ($handler === false) {
            throw new Exception("Can not open log file");
        }

        $runAt = Carbon::createFromFormat('U.u', $report->getStartTime());

        fwrite($handler, "----------------------------CronReport----------------------------\n");
        fwrite($handler, "| Id: " . $cronJob->id . "\n");
        fwrite($handler, "| Name: " . $cronJob->name . "\n");
        fwrite($handler, "| UserId: " . CronBundle::getUser() . "\n");
        fwrite($handler, "| RunAt: " . $runAt->format("Y-m-d H:i:s.u") . "\n");
        fwrite($handler, "| RunTime: " . ($report->getEndTime() - $report->getStartTime()) . "\n");
        fwrite($handler, "| ExitCode: " . $shellJob->getProcess()->getExitCode() . "\n");
        fwrite($handler, "------------------------------Output------------------------------\n");
        fwrite($handler, $jobOutput);
        fwrite($handler, "----------------------------Output End----------------------------\n\n\n");

        fclose($handler);
    }

    /**
     * @param JobReport $report
     * @param array $logConfig
     * @throws Exception
     */
    private static function createSingleFileOutputReport(JobReport $report, array $logConfig)
    {
        /** @var ShellJob $shellJob */
        $shellJob = $report->getJob();
        $jobOutput = implode("\n", $report->getOutput());
        if ($logConfig['truncate']) {
            $jobOutput = substr($jobOutput, 0, intval($logConfig['truncate']));
        }
        /** @var CronJob $cronJob */
        $cronJob = $shellJob->raw;

        $dirPath = $logConfig['dirPath'];
        $filename = $logConfig['fileFormat'];

        $logPH = [
            '%datetime%' => now()->format('Ymd_His'),
            '%jobId%'    => $cronJob->id,
            '%jobName%'  => self::slugify($cronJob->name),
        ];

        $filename = strtr($filename, $logPH);

        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $realFilename = rtrim($dirPath, '/') . '/' . ltrim($filename, '/');

        $handler = fopen($realFilename, 'w');
        if ($handler === false) {
            throw new Exception("Can not open log file");
        }

        $runAt = Carbon::createFromFormat('U.u', $report->getStartTime());

        fwrite($handler, "----------------------------CronReport----------------------------\n");
        fwrite($handler, "| Id: " . $cronJob->id . "\n");
        fwrite($handler, "| Name: " . $cronJob->name . "\n");
        fwrite($handler, "| UserId: " . CronBundle::getUser() . "\n");
        fwrite($handler, "| RunAt: " . $runAt->format("Y-m-d H:i:s.u") . "\n");
        fwrite($handler, "| RunTime: " . ($report->getEndTime() - $report->getStartTime()) . "\n");
        fwrite($handler, "| ExitCode: " . $shellJob->getProcess()->getExitCode() . "\n");
        fwrite($handler, "------------------------------Output------------------------------\n");
        fwrite($handler, $jobOutput);
        fwrite($handler, "----------------------------Output End----------------------------\n");

        fclose($handler);
    }

    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
