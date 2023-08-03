@props(['isSelected'])

<div {{ $attributes->class([
    'table-filter-item flex items-center space-x-2 px-8 py-2 md:px-4 group cursor-pointer transition-default',
    'hover:bg-theme-secondary-100 dark:hover:bg-theme-secondary-800' => ! $isSelected,
]) }}>
    {{ $slot }}
</div>
