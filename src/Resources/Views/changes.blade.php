<dl class="row">
    @if (empty($changes))
        <dt class="col-12">There is no changes</dt>
    @else
        <dt class="col-4 offset-4">Old value</dt>
        <dt class="col-4">New value</dt>
        @foreach($changes as $attr => $vals)
            <dt class="col-4">{{ ucfirst($attr) }}</dt>
            @if ($attr == "enabled")
                <dd class="col-4">{{ $vals['old'] == '1' ? 'Yes' : 'No' }}</dd>
                <dd class="col-4">{{ $vals['new'] == '1' ? 'Yes' : 'No' }}</dd>
            @else
                <dd class="col-4">{{ $vals['old'] }}</dd>
                <dd class="col-4">{{ $vals['new'] }}</dd>
            @endif
        @endforeach
    @endif
</dl>
