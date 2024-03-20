@props(['delegate'])

@if ($delegate->justMissed() || $delegate->keepsMissing())
    <div
        data-tippy-content=""
        class="text-theme-warning-900"
    >
        <x-ark-icon
            name="alert-triangle"
            size="sm"
        />
    </div>
@endif
