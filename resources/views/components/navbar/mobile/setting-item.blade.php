@props(['title'])

<div {{ $attributes->class('flex items-center justify-between') }}>
    <div class="font-semibold dark:text-theme-secondary-500">{{ $title }}</div>

    <div>
        {{ $slot }}
    </div>
</div>
