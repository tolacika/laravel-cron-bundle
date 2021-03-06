<?php

namespace Tolacika\CronBundle\Http\Controllers;

use Cron\Exception\InvalidPatternException;
use Cron\Validator\CrontabValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Response;
use Tolacika\CronBundle\CronBundle;
use Tolacika\CronBundle\Http\Middleware\Authenticate;
use Tolacika\CronBundle\Models\CronJob;
use Tolacika\CronBundle\Models\CronReport;

class CronBundleController extends Controller
{
    use ValidatesRequests;

    /**
     * CronBundleController constructor.
     */
    public function __construct()
    {
        $this->middleware(config('cron-bundle.dashboardMiddleware'));
        $this->middleware(Authenticate::class);
    }

    /**
     * Display a listing all Jobs.
     * Optionally with the deleted jobs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $showDeleted = !!$request->get('showDeleted', false);

        return view('cron-bundle::index', ['showDeleted' => $showDeleted]);
    }

    /**
     * Show the form for creating a new job.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cron-bundle::create');
    }

    /**
     * Store a newly created job in database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name'     => "required|max:50",
            'command'  => "required",
            'attrs'    => "nullable",
            'schedule' => 'required',
        ]);

        $validator->after(function ($validator) use ($request) {
            /** @var Validator $validator */

            // Validates the name of the new job, adds an error if the name already in use.
            if ($request->has('name') && !empty($request->get('name', ''))) {
                if (CronJob::where('name', $request->get('name'))->count() > 0) {
                    $validator->errors()->add('name', trans('validation.unique', ['attribute' => 'name']));
                }
            }

            // Validates the command and arguments
            $fullCommand = $baseCommand = trim($request->get('command'));
            if (!empty($baseCommand)) {
                if (!CronBundle::isCommandAllowed($baseCommand)) {
                    // Add error if the command is in blacklist or not in whitelist
                    $validator->errors()->add("command", "The command is not allowed");
                }
                if ($request->has('args') && !empty($request->get('args', ''))) {
                    $fullCommand .= " " . trim($request->get('args'));
                }
                if (mb_strlen($fullCommand) >= 191) {
                    // Add an error if the full command's length is greater than the database field length
                    $validator->errors()->add("command", trans('validation.max.string', ['attribute' => 'command', 'max' => '191']));
                }
            }

            // Validates schedule based by CronTableValidator
            if ($request->has('schedule') && !empty($request->get('schedule', ''))) {
                $cronValidator = new CrontabValidator();
                try {
                    $cronValidator->validate($request->get('schedule', ''));
                } catch (InvalidPatternException $exception) {
                    $validator->errors()->add('schedule', "Invalid schedule pattern:<br>" . $exception->getMessage());
                }
            }
        });

        if ($validator->fails()) {
            return redirect(route('cron-bundle.create'))->withErrors($validator)->withInput($request->all());
        }


        $fullCommand = trim($request->get('command'));
        if ($request->has('args') && !empty($request->get('args', ''))) {
            $fullCommand .= " " . trim($request->get('args'));
        }

        // Creates a new job
        $job = new CronJob();
        $job->name = $request->get('name');
        $job->command = $fullCommand;
        $job->schedule = $request->get('schedule');
        $job->description = $request->get('description', '') ?? '';
        $job->enabled = $request->has('enabled') ? '1' : '0';

        $job->save();

        return redirect(route('cron-bundle.index'))->with('success', "The job saved successfully");
    }

    /**
     * Display the specified job.
     *
     * @param $jobId
     * @return \Illuminate\Http\Response
     */
    public function show($jobId)
    {
        $job = CronJob::withTrashed()->find($jobId);
        if (!$job) {
            abort(404);
        }

        return view('cron-bundle::show', ['job' => $job]);
    }

    /**
     * Show the form for editing the specified job.
     *
     * @param $jobId
     * @return \Illuminate\Http\Response
     */
    public function edit($jobId)
    {
        $job = CronJob::withTrashed()->find($jobId);

        return view('cron-bundle::edit', ['job' => $job]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param CronJob $job
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CronJob $job)
    {
        $validator = \Validator::make($request->all(), [
            'name'     => "required|max:50",
            'command'  => "required",
            'attrs'    => "nullable",
            'schedule' => 'required',
        ]);

        $validator->after(function ($validator) use ($request, $job) {
            /** @var Validator $validator */

            // Validates the name of the new job, adds an error if the name already in use.
            if ($request->has('name') && !empty($request->get('name', ''))) {
                if (CronJob::where('name', $request->get('name'))->where('id', '!=', $job->id)->count() > 0) {
                    $validator->errors()->add('name', trans('validation.unique', ['attribute' => 'name']));
                }
            }

            // Validates the command and arguments
            $fullCommand = $baseCommand = trim($request->get('command'));
            if (!empty($baseCommand)) {
                // Add error if the command is in blacklist or not in whitelist
                if (!CronBundle::isCommandAllowed($baseCommand)) {
                    $validator->errors()->add("command", "The command is not allowed");
                }
                if ($request->has('args') && !empty($request->get('args', ''))) {
                    $fullCommand .= " " . trim($request->get('args'));
                }
                if (mb_strlen($fullCommand) >= 191) {
                    // Add an error if the full command's length is greater than the database field length
                    $validator->errors()->add("command", trans('validation.max.string', ['attribute' => 'command', 'max' => '191']));
                }
            }

            // Validates schedule based by CronTableValidator
            if ($request->has('schedule') && !empty($request->get('schedule', ''))) {
                $cronValidator = new CrontabValidator();
                try {
                    $cronValidator->validate($request->get('schedule', ''));
                } catch (InvalidPatternException $exception) {
                    $validator->errors()->add('schedule', "Invalid schedule pattern:<br>" . $exception->getMessage());
                }
            }
        });

        if ($validator->fails()) {
            return redirect(route('cron-bundle.edit'))->withErrors($validator)->withInput($request->all());
        }


        $fullCommand = trim($request->get('command'));
        if ($request->has('args') && !empty($request->get('args', ''))) {
            $fullCommand .= " " . trim($request->get('args'));
        }

        // Edits the job
        $job->name = $request->get('name');
        $job->command = $fullCommand;
        $job->schedule = $request->get('schedule');
        $job->description = $request->get('description', '') ?? '';
        $job->enabled = $request->has('enabled') ? '1' : '0';

        $job->save();

        return redirect(route('cron-bundle.index'))->with('success', "The job saved successfully");
    }

    /**
     * Remove the specified job from storage with soft delete.
     *
     * @param Request $request
     * @param CronJob $job
     * @return void
     * @throws \Exception
     */
    public function destroy(Request $request, CronJob $job)
    {
        if ($request->isMethod("POST")) {
            $job->delete();

            return redirect(route('cron-bundle.index'))->with('success', "The job deleted successfully");
        }

        return view('cron-bundle::destroy', ['job' => $job]);
    }

    /**
     * Returns package assets
     *
     * @param $file
     * @return \Illuminate\Http\Response
     */
    public function assetjs($file)
    {
        $filename = __DIR__ . "/../../Resources/assets/js/" . $file;

        if (!file_exists($filename)) {
            abort(404);
        }

        return Response::make(file_get_contents($filename))->header("Content-type", "application/javascript");
    }

    /**
     * Returns package assets
     *
     * @param $file
     * @return \Illuminate\Http\Response
     */
    public function assetcss($file)
    {
        $filename = __DIR__ . "/../../Resources/assets/css/" . $file;

        if (!file_exists($filename)) {
            abort(404);
        }

        return Response::make(file_get_contents($filename))->header("Content-type", "text/css");
    }

    /**
     * Returns the job report output for the job details page
     *
     * @param CronReport $report
     * @return \Illuminate\Http\Response
     */
    public function reportOutput(CronReport $report)
    {
        return Response::make($report->output)->header('Content-type', 'text-plain');
    }
}
