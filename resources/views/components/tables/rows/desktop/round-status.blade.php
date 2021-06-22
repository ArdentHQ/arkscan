@if ($model->isNext())
    <span class="font-bold text-theme-primary-400 round-status">
        <x-ark-icon name="app-status-waiting" size="md" />
    </span>
@elseif ($model->isPending())
    <span class="font-bold text-theme-gray-400 round-status">
        <x-ark-icon name="app-status-waiting" size="md" />
    </span>
@else
    @if ($model->keepsMissing())
        <span class="flex items-center font-bold text-theme-danger-400 round-status">
            <x-ark-icon name="app-status-undone" size="md" />
            <span class="hidden ml-2 lg:inline">
                @lang('pages.delegates.danger', [ExplorerNumberFormatter::number($model->missedCount())])
            </span>
        </span>
    @elseif ($model->justMissed())
        <span class="flex items-center font-bold text-theme-warning-400 round-status">
            <x-ark-icon name="app-status-missed" size="md" />
            <span class="hidden ml-2 lg:inline">
                @lang('pages.delegates.warning')
            </span>
        </span>
    @else
        {{-- @TODO: do we want an explicit check for forged here or just assume that they forged if both of the missed checks are false? --}}
        <span class="flex items-center font-bold text-theme-success-400 round-status">
            <x-ark-icon name="app-status-done" size="md" />
            <span class="hidden ml-2 lg:inline">
                @lang('pages.delegates.success')
            </span>
        </span>
    @endif
@endif
