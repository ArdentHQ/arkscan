@props(['size'])

<div {{ $attributes->class([
    'animate-pulse rounded-full bg-theme-secondary-300 dark:bg-theme-secondary-800',
    $size,
]) }}></div>
