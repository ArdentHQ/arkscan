@props([
    'color' => 'bg-theme-secondary-200 dark:bg-theme-dark-950 text-theme-secondary-200 dark:text-theme-dark-950',
])

<hr {{ $attributes->class([
    'h-1 md:hidden',
    $color,
]) }} />
