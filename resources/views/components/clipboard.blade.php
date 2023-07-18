@props([
    'value',
    'tooltip' => null,
    'colors' => 'text-theme-primary-300 dark:text-theme-secondary-600 hover:text-theme-primary-400 dark:hover:text-theme-secondary-400',
])

<x-ark-clipboard
    :value="$value"
    :tooltip-content="$tooltip"
    :class="Arr::toCssClasses([
        'flex items-center w-auto h-auto ml-2',
        $colors,
    ])"
    no-styling
/>
