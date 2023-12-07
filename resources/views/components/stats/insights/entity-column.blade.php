@props([
    'title',
    'showOnMobile' => false,
])

<div {{ $attributes->class([
    'md:flex md:justify-between',
    'space-y-2 md:space-y-0' => $showOnMobile,
]) }}>
    <div @class([
        'md:flex items-center',
        'hidden' => ! $showOnMobile,
    ])>
        <span>{{ $title }}</span>

        <span class="hidden md:inline">:</span>
    </div>

    <div class="text-theme-secondary-900 dark:text-theme-dark-50">
        {{ $slot }}
    </div>
</div>
