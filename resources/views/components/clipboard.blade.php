@props([
    'value',
    'tooltip' => null,
    'colors' => 'text-theme-secondary-700 hover:text-theme-primary-700 dark:text-theme-dark-300 dark:hover:text-theme-dark-50',
])

<x-ark-clipboard
    :value="$value"
    :tooltip-content="$tooltip"
    :class="Arr::toCssClasses(['flex items-center w-auto h-auto ml-2 transition-default',
        $colors,
        $attributes->get('class'),
    ])"
    no-styling
/>
