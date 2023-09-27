@props(['title'])

<div {{ $attributes->class('space-y-2') }}>
    <div class="text-sm font-semibold">
        {{ $title }}
    </div>

    <div class="font-semibold text-theme-secondary-900 dark:text-theme-secondary-50">
        {{ $slot }}
    </div>
</div>
