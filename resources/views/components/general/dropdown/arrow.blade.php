@props([
    'key' => 'dropdownOpen',
    'color' => 'text-theme-secondary-700 dark:text-theme-dark-200',
])

<div
    {{ $attributes->class('transition-default') }}
    :class="{ 'rotate-180': {{ $key }} }"
>
    <x-ark-icon
        name="arrows.chevron-down-small"
        size="w-3 h-3"
        :class="$color"
    />
</div>
