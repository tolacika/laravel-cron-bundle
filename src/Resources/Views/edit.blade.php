<?php /** @var \Tolacika\CronBundle\Models\CronJob $job */ ?>
@extends('cron-bundle::base')

@section('card-title', "Edit task: " . $job->name)

@section('content')
    <form method="POST" action="{{ route('cron-bundle.update', [$job]) }}">
        {{ csrf_field() }}
        @if(isset($errors))
            @foreach($errors->all() as $msg)
                <div class="alert alert-danger">
                    {!! $msg !!}
                </div>
            @endforeach
        @endif
        <div class="form-group">
            <label for="create-name">Name:</label>
            <input type="text" name="name" id="create-name" class="form-control" placeholder="Name of the new task"
                   value="{{ old("name") ?? $job->name }}" aria-describedby="nameHelp" autocomplete="off">
            <small id="nameHelp" class="form-text text-muted">Must be unique</small>
        </div>
        <div class="form-group">
            <label for="create-command">Command:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Command:</span>
                </div>
                <select name="command" id="create-command" class="form-control text-monospace">
                    <option value="">--- Select a command ---</option>
                    @foreach(\Tolacika\CronBundle\CronBundle::getAvailableCommands() as $command)
                        <option value="{{ $command }}"
                                @if ((old('command') ?? $job->getCommandPart()) == $command) selected @endif>{{ $command }}</option>
                    @endforeach
                </select>
                <div class="input-group-addon">
                    <span class="input-group-text">Arguments:</span>
                </div>
                <input type="text" name="args" class="form-control text-monospace"
                       value="{{ old("args") ?? $job->getArgumentPart() }}"
                       autocomplete="off">
            </div>
            <small id="commandHelp" class="form-text text-muted">
                Only <code>artisan</code> commands supported.
                You can edit command white/blacklist in <code>config/cron-bundle.php</code>
            </small>
        </div>
        <div class="form-group">
            <label for="create-schedule">Schedule (<a href="https://github.com/Cron/Cron#crontab-syntax"
                                                      target="_blank">help</a>):</label>
            <input type="text" name="schedule" id="create-schedule" class="form-control"
                   placeholder="The schedule of the new task"
                   value="{{ old("schedule") ?? $job->schedule }}" aria-describedby="scheduleHelp" autocomplete="off">
            <label for="create-schedule-select" class="form-text">Or select from the list</label>
            <select name="schedule-select" id="create-schedule-select" class="form-control">
                <option value=""></option>
                @foreach(\Tolacika\CronBundle\CronBundle::getPredefinedSchedules() as $sch => $text)
                    <option value="{{ $sch }}"
                            @if($sch == (old('schedule') ?? $job->schedule)) selected @endif>{{ $text }} ({{ $sch }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="create-desc">Description:</label>
            <textarea name="description" id="create-desc"
                      class="form-control">{{ old("description") ?? $job->description }}</textarea>
        </div>
        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" name="enabled" id="create-enabled"
                       @if (old("enabled") ?? $job->isEnabled()) checked @endif value="true">
                <label for="create-enabled" class="custom-control-label">Enabled</label>
            </div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('cron-bundle.index') }}" class="btn btn-default">Cancel</a>
            <a href="{{ route('cron-bundle.destroy', [$job]) }}" class="btn btn-danger">Delete</a>
        </div>
    </form>
@endsection
