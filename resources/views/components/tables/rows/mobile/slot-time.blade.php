@if ($model->isNext())
    @lang('pages.delegates.now')
@elseif ($model->isDone())
    @lang('pages.delegates.completed')
@else
    {{ $model->forgingAt()->diffForHumans() }}
@endif
