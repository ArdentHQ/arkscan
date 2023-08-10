@props([
    'colors' => 'border-transparent bg-theme-secondary-200 dark:border-theme-secondary-800 dark:text-theme-secondary-500',
])

<div {{ $attributes->class([
    'text-xs font-semibold rounded border dark:bg-transparent px-[3px] py-[2px] leading-3.75',
    $colors,
]) }}>
    {{ $slot }}
</div>
