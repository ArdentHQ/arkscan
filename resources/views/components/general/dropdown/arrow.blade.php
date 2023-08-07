@props([
    'key' => 'dropdownOpen',
])

<div
    {{ $attributes->class('transition-default') }}
    :class="{ 'rotate-180': {{ $key }} }"
>
    <x-ark-icon
        name="arrows.chevron-down-small"
        size="w-3 h-3"
        class="text-theme-secondary-700 dark:text-theme-secondary-200"
    />
</div>
