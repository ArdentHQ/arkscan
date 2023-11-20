@props(['title'])

<div {{ $attributes->class('flex items-center justify-between') }}>
    <div class="font-semibold dark:text-theme-dark-200">{{ $title }}</div>

    <div>
        {{ $slot }}
    </div>
</div>
