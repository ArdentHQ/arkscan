@props([
    'transaction',
    'title',
    'value' => null,
    'valueClass' => null,
])

@php
    $headerWidth = 'w-[87px]';
    if ($transaction->isVoteCombination()) {
        $headerWidth = 'w-[109px]';
    } elseif ($transaction->isLegacy()) {
        $headerWidth = 'w-[110px]';
    }
@endphp

<div {{ $attributes->class('flex items-center space-x-4') }}>
    <div @class([
        'whitespace-nowrap',
        $headerWidth,
    ])>
        {{ $title }}
    </div>

    <div @class([
        'flex-1 space-y-3 text-right sm:text-left text-theme-secondary-900 dark:text-theme-dark-50',
        $valueClass,
    ])>
        @if ($value)
            {{ $value }}
        @elseif (strlen($slot) > 0)
            {{ $slot }}
        @else
            @lang('general.na')
        @endif
    </div>
</div>
