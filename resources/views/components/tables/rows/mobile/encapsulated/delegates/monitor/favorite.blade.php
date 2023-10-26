@props(['model'])

<div {{ $attributes->class('flex space-x-2 items-center font-semibold') }}>
    <x-ark-icon name="star" />

    <div>@lang('tables.delegate-monitor.favorite')</div>
</div>
