@props(['isSelected'])

<div {{ $attributes->class([
    'table-filter-item flex items-center space-x-2 py-2 px-4 group cursor-pointer transition-default',
    'hover:bg-theme-secondary-100 dark:hover:bg-theme-secondary-800' => ! $isSelected,
    'bg-theme-primary-50 dark:bg-black' => $isSelected,
]) }}>
    {{ $slot }}
</div>
