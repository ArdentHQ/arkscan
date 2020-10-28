<div wire:loading.class="h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
<div wire:loading.class="hidden">
    @if ($model->forgingAt()->isPast())
        @lang('pages.monitor.completed')
    @else
        {{ $model->forgingAt()->diffForHumans() }}
    @endif
</div>
