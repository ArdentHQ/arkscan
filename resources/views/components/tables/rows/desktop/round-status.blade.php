@if ($model->isNext())
    <span class="font-bold text-theme-primary-400">
        <x-icon name="app-status-waiting" size="lg" />
    </span>
@elseif ($model->isPending())
    <span class="font-bold text-theme-gray-400">
        <x-icon name="app-status-waiting" size="lg" />
    </span>
@else
    @if ($model->keepsMissing())
        <span class="flex items-center font-bold text-theme-danger-400">
            <x-icon name="app-status-undone" size="lg" />
            <span class="ml-2">
                @lang('pages.monitor.danger', [App\Services\NumberFormatter::number($model->missedCount())])
            </span>
        </span>
    @elseif ($model->justMissed())
        <span class="flex items-center font-bold text-theme-warning-400">
            <x-icon name="app-status-missed" size="lg" />
            <span class="ml-2">
                @lang('pages.monitor.warning')
            </span>
        </span>
    @elseif ($model->hasForged())
        <span class="flex items-center font-bold text-theme-success-400">
            <x-icon name="app-status-done" size="lg" />
            <span class="ml-2">
                @lang('pages.monitor.success')
            </span>
        </span>
    @endif
@endif
