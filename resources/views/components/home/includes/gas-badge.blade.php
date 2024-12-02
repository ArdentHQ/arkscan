@props([
    'title',
    'value',
])

<div {{ $attributes->class([
    'text-sm md:text-xs text-theme-secondary-900 dark:text-theme-dark-50 md:bg-theme-secondary-200',
    'md:dark:bg-theme-dark-800 md:px-1.5 md:py-[3px] md:rounded leading-4.25 whitespace-nowrap',
    'sm:border-l border-theme-secondary-300 dark:border-theme-dark-700 sm:pl-2 first:pl-0 first:md:px-1.5 first:border-l-0 md:border-0',
]) }}>
    <span>
        {{ $title }}:
    </span>

    <span>
        {{ $value }}
    </span>
</div>
