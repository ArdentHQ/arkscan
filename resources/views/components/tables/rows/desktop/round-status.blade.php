@if ($model->isNext())
    <span class="font-bold text-theme-primary-400">
        <x-ark-icon name="app-status-waiting" size="lg" />
    </span>
@elseif ($model->isPending())
    <span class="font-bold text-theme-gray-400">
        <x-ark-icon name="app-status-waiting" size="lg" />
    </span>
@else
    @if ($model->keepsMissing())
        <span class="flex items-center font-bold text-theme-danger-400">
            <x-ark-icon name="app-status-undone" size="lg" />
            <span class="hidden ml-2 lg:inline">
                @lang('pages.delegates.danger', [App\Services\NumberFormatter::number($model->missedCount())])
            </span>
        </span>
    @elseif ($model->justMissed())
        <span class="flex items-center font-bold text-theme-warning-400">
            <x-ark-icon name="app-status-missed" size="lg" />
            <span class="hidden ml-2 lg:inline">
                @lang('pages.delegates.warning')
            </span>
        </span>
    @else
        {{-- @TODO: do we want an explicit check for forged here or just assume that they forged if both of the missed checks are false? --}}
        <span class="flex items-center font-bold text-theme-success-400">
            <x-ark-icon name="app-status-done" size="lg" />
            <span class="hidden ml-2 lg:inline">
                @lang('pages.delegates.success')
            </span>
        </span>
    @endif
@endif
