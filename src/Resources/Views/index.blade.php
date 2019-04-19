@extends('cron-bundle::base')

@section('card-title', "Task list")

@section('card-buttons')
    <a href="{{ route('cron-bundle.create') }}" class="btn btn-success pull-right btn-sm">Add task</a>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Schedule</th>
                <th>Description</th>
                <th>Enabled</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach(\Tolacika\CronBundle\Models\CronJob::getAllJobs() as $job)
                <tr>
                    <td>#{{ $job->id }}</td>
                    <td>{{ $job->name }}</td>
                    <td class="text-monospace">{{ $job->schedule }}</td>
                    <td>{{ $job->description }}</td>
                    <td>
                        @if ($job->isEnabled())
                            <span class="badge badge-success">Yes</span>
                        @else
                            <span class="badge badge-danger">No</span>
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-primary btn-sm" href="{{ route('cron-bundle.edit', ['job' => $job->id]) }}">Edit</a>
                        <a class="btn btn-secondary btn-sm" href="{{ route('cron-bundle.show', ['job' => $job->id]) }}">Reports</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
