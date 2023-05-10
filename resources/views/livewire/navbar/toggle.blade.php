@props([
    'activeIcon',
    'inactiveIcon',
])

<button
    wire:click="toggle"
    {{ $attributes->class('flex items-center justify-center w-8 h-8 border text-theme-secondary-700 dark:text-theme-secondary-600 border-theme-secondary-300 hover:bg-theme-secondary-300 dark:border-theme-secondary-800 dark:hover:bg-theme-secondary-800 rounded transition-default') }}
>
    <x-ark-icon
        :name="$this->icon()"
        size="sm"
    />
</button>
