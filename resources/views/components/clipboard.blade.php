@props([
    'value',
    'tooltip' => null,
    'colors' => 'text-theme-primary-400 dark:text-theme-dark-300 hover:text-theme-primary-700 dark:hover:text-theme-dark-blue-500',
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
