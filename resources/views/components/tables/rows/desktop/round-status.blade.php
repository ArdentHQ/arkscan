@if ($model->isNext())
    <span class="font-bold text-theme-secondary-500">
        @svg('app-status-waiting', 'w-8 h-8')
    </span>
@elseif ($model->isPending())
    <span class="font-bold text-theme-gray-500">
        @svg('app-status-waiting', 'w-8 h-8')
    </span>
@else
    @if ($model->keepsMissing())
        <span class="font-bold text-theme-danger-500">
            @svg('app-status-undone', 'w-8 h-8')
            @lang('pages.monitor.danger', [App\Services\NumberFormatter::number($model->missedCount())])
        </span>
    @elseif ($model->justMissed())
        <span class="font-bold text-theme-warning-500">
            @svg('app-status-missed', 'w-8 h-8')
            @lang('pages.monitor.warning')
        </span>
    @elseif ($model->hasForged())
        <span class="font-bold text-theme-success-500">
            @svg('app-status-done', 'w-8 h-8')
            @lang('pages.monitor.success')
        </span>
    @endif
@endif
