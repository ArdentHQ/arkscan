@props([
    'colors' => 'border-transparent bg-theme-secondary-200 dark:border-theme-dark-800 dark:text-theme-dark-500',
])

<div {{ $attributes->class([
    'text-xs font-semibold rounded border dark:bg-transparent px-[3px] py-[2px] leading-3.75 shrink-0',
    $colors,
]) }}>
    {{ $slot }}
</div>
