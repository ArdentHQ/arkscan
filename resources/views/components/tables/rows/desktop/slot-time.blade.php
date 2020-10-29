@if ($model->forgingAt()->isPast())
    @lang('pages.monitor.completed')
@else
    {{ $model->forgingAt()->diffForHumans() }}
@endif
