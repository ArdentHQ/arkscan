<div>
    @lang('labels.productivity')

    <span>
        @if($model->productivity() >= 0)
            <x-percentage>
                {{ $model->productivity() }}
            </x-percentage>
        @else
            @lang('generic.not-available')
        @endif
    </span>
</div>
