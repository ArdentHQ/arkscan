@props(['exchange'])

@php
    $pairs = array_filter([
        'btc' => $exchange->btc,
        'eth' => $exchange->eth,
        'stablecoins' => $exchange->stablecoins,
        'other' => $exchange->other,
    ], fn ($enabled) => $enabled);
@endphp

<div {{ $attributes->class(['flex divide-x divide-theme-secondary-300 dark:divide-theme-dark-800 space-x-2 text-theme-secondary-900 dark:text-theme-dark-50 font-semibold leading-4.25']) }}>
    @foreach ($pairs as $pair => $value)
        <div class="pl-2 first:pl-0">
            @lang('pages.exchanges.pair.' . $pair)
        </div>
    @endforeach
</div>
