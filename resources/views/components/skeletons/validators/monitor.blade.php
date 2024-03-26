@props([
    'rowCount' => 51,
])

@if (! $this->isReady || ! $this->hasValidators)
    <div wire:key="skeleton:validators:not-ready">
        <x-tables.desktop.skeleton.validators.monitor :row-count="$rowCount" />

        <x-tables.mobile.skeleton.validators.monitor />
    </div>
@else
    {{ $slot }}
@endif
