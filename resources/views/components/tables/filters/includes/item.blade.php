@props(['isSelected'])

<div {{ $attributes->class([
    'group/filter-item table-filter-item flex items-center space-x-2 px-8 py-[0.875rem] my-[0.125rem] md:px-4 group cursor-pointer transition-default rounded-lg',
    'text-theme-secondary-700 dark:text-theme-dark-200 hover:bg-theme-secondary-200 hover:dark:bg-theme-dark-950 hover:text-theme-secondary-900 hover:dark:text-theme-dark-50' => ! $isSelected,
    'group/filter-item text-theme-primary-600 dark:text-theme-dark-50 bg-theme-secondary-200 dark:bg-theme-dark-950' => $isSelected,
]) }}>
    {{ $slot }}
</div>
