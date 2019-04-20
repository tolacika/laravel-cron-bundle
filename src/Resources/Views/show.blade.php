<?php /** @var \Tolacika\CronBundle\Models\CronJob $job */ ?>
@extends('cron-bundle::base')

@section('card-title', 'Job details: ' . $job->name)

@section('card-buttons')
    <a href="{{ route('cron-bundle.index') }}" class="btn btn-success pull-left btn-sm mr-3">Back</a>
@endsection

@section('content')
    <div class="accordion" id="jobDetailsColl">
        <div class="card">
            <div class="card-header" id="jobInfo" data-toggle="collapse" data-target="#jobInfoColl" aria-expanded="true"
                 aria-controls="collapseOne">
                <h5 class="mb-0">
                    Basic information
                </h5>
            </div>

            <div id="jobInfoColl" class="collapse show" aria-labelledby="jobInfo" data-parent="#jobDetailsColl">
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Name:</dt>
                        <dd class="col-sm-9">{{ $job->name }}</dd>

                        <dt class="col-sm-3">Command:</dt>
                        <dd class="col-sm-9">{{ $job->command }}</dd>

                        <dt class="col-sm-3">Schedule:</dt>
                        <dd class="col-sm-9">{{ $job->schedule }}</dd>

                        <dt class="col-sm-3">Description:</dt>
                        <dd class="col-sm-9">{!! nl2br($job->description) !!}</dd>

                        <dt class="col-sm-3">Enabled:</dt>
                        <dd class="col-sm-9">{{ $job->isEnabled() ? "Yes" : "No" }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="jobReports" data-toggle="collapse" data-target="#jobReportsColl"
                 aria-expanded="false" aria-controls="collapseTwo">
                <h5 class="mb-0">
                    Reports
                </h5>
            </div>
            <div id="jobReportsColl" class="collapse" aria-labelledby="jobReports" data-parent="#jobDetailsColl">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Run at</th>
                                <th>Run time</th>
                                <th>Exit code</th>
                                <th>Output</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($job->reports()->orderBy('run_at', 'DESC')->get() as $report)
                                <?php /** @var \Tolacika\CronBundle\Models\CronReport $report */ ?>
                                <tr>
                                    <td>{{ $report->run_at }}</td>
                                    <td>{{ $report->run_time }}</td>
                                    <td><code>{{ $report->exit_code }}</code></td>
                                    <td>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#outputModal" data-report-id="{{ $report->id }}">Output</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="jobLogs" data-toggle="collapse" data-target="#jobLogsColl"
                 aria-expanded="false" aria-controls="collapseThree">
                <h5 class="mb-0">
                    Logs
                </h5>
            </div>
            <div id="jobLogsColl" class="collapse" aria-labelledby="jobLogs" data-parent="#jobDetailsColl">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Action</th>
                                <th>Changes</th>
                                <th>User id</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($job->logs()->orderBy('created_at', 'DESC')->get() as $log)
                                <?php /** @var \Tolacika\CronBundle\Models\CronLog $log */ ?>
                                <tr>
                                    <td>{{ $log->created_at }}</td>
                                    <td>{{ ucfirst($log->type) }}</td>
                                    <td>{!! $log->formatModifies() !!}</td>
                                    <td>{{ $log->user_id }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="outputModal" tabindex="-1" role="dialog" aria-labelledby="outputModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="outputModalLabel">Cron job output</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <samp>Loading...</samp>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
