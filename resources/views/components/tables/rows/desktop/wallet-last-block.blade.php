@if($model->justMissed())
    <span>@lang('generic.not-available')</span>
@elseif(! $model->isDone())
    <span>@lang('generic.to-be-done')</span>
@else
    <a href="{{ route('block', $model->lastBlock()['id']) }}" class="font-semibold link">
        <x-truncate-middle>
            {{ $model->lastBlock()['id'] }}
        </x-truncate-middle>
    </a>
@endif
