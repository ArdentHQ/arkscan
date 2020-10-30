@if ($model->isNext())
    <span class="font-bold text-theme-primary-400">
        @svg('app-status-waiting', 'w-8 h-8')
    </span>
@elseif ($model->isPending())
    <span class="font-bold text-theme-gray-400">
        @svg('app-status-waiting', 'w-8 h-8')
    </span>
@else
    @if ($model->keepsMissing())
        <span class="flex items-center font-bold text-theme-danger-400">
            @svg('app-status-undone', 'w-8 h-8')
            <span class="ml-2">
                @lang('pages.monitor.danger', [App\Services\NumberFormatter::number($model->missedCount())])
            </span>
        </span>
    @elseif ($model->justMissed())
        <span class="flex items-center font-bold text-theme-warning-400">
            @svg('app-status-missed', 'w-8 h-8')
            <span class="ml-2">
                @lang('pages.monitor.warning')
            </span>
        </span>
    @elseif ($model->hasForged())
        <span class="flex items-center font-bold text-theme-success-400">
            @svg('app-status-done', 'w-8 h-8')
            <span class="ml-2">
                @lang('pages.monitor.success')
            </span>
        </span>
    @endif
@endif
