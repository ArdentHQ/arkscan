@props(['validator'])

@if ($validator->keepsMissing())
    <div
        data-tippy-content="@lang('pages.validator-monitor.missed_blocks_tooltip', [
            'blocks' => $validator->blocksSinceLastForged(),
            'time'   => $validator->durationSinceLastForged(),
        ])"
        class="text-theme-warning-900"
    >
        <x-ark-icon
            name="alert-triangle"
            size="sm"
        />
    </div>
@endif
