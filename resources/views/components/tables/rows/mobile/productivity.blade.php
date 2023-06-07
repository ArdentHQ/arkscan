<div>
    <span class="font-semibold">
        @lang('labels.productivity')
    </span>

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
