@if ($model->isNext())
    @lang('pages.monitor.now')
@elseif ($model->isDone())
    @lang('pages.monitor.completed')
@else
    {{ $model->forgingAt()->diffForHumans() }}
@endif
