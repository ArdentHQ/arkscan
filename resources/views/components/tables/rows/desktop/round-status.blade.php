<div wire:loading.class="h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
<div wire:loading.class="hidden">
    @if (optional($model->forgingAt())->isFuture())
        <span class="font-bold text-theme-gray-500">
            @svg('app-status-waiting', 'w-8 h-8')
        </span>
    @else
        @if ($model->isSuccess())
            <span class="font-bold text-theme-success-500">
                @svg('app-status-done', 'w-8 h-8')
                @lang('pages.monitor.success')
            </span>
        @endif

        @if ($model->isWarning())
            <span class="font-bold text-theme-warning-500">
                @svg('app-status-missed', 'w-8 h-8')
                @lang('pages.monitor.warning')
            </span>
        @endif

        @if ($model->isDanger())
            <span class="font-bold text-theme-danger-500">
                @svg('app-status-undone', 'w-8 h-8')
                @lang('pages.monitor.danger', [$model->missedCount()])
            </span>
        @endif
    @endif
</div>
