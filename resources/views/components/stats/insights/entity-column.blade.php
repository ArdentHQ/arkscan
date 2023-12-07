@props([
    'title',
    'showOnMobile' => false,
])

<div {{ $attributes->class([
    'md:flex md:justify-between',
    'space-y-2 md:space-y-0' => $showOnMobile,
]) }}>
    <div @class([
        'hidden md:flex items-center' => ! $showOnMobile,
    ])>
        <span>{{ $title }}</span>

        <span class="hidden md:inline">:</span>
    </div>

    <div class="text-theme-secondary-900">
        {{ $slot }}
    </div>
</div>
