@props(['delegate'])

@if ($delegate->keepsMissing())
    <div
        data-tippy-content="@lang('pages.delegate-monitor.missed_blocks_tooltip', [
            'blocks' => $delegate->blocksSinceLastForged(),
            'time'   => $delegate->durationSinceLastForged(),
        ])"
        class="text-theme-warning-900"
    >
        <x-ark-icon
            name="alert-triangle"
            size="sm"
        />
    </div>
@endif
