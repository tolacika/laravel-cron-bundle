<?php /** @var \Tolacika\CronBundle\Models\CronJob $job */ ?>
@extends('cron-bundle::base')

@section('card-title', 'Delete job: ' . $job->name)

@section('content')
    <form method="POST" action="{{ route('cron-bundle.destroy', [$job]) }}">
        {{ csrf_field() }}
        <h2>Are you sure to delete this task?</h2>
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

            <dd class="col-sm-9 offset-sm-3">
                <button class="btn btn-danger" type="submit">Delete</button>
                <a href="{{ route('cron-bundle.index') }}" class="btn btn-default">Cancel</a>
            </dd>
        </dl>
    </form>
@endsection
