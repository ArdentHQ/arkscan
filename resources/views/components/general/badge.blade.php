@props([
    'colors' => 'border-transparent bg-theme-secondary-200 dark:border-theme-dark-700 text-theme-secondary-700 dark:text-theme-dark-200',
])

<div {{ $attributes->class([
    'text-xs font-semibold rounded border dark:bg-transparent px-[3px] py-[2px] leading-3.75 shrink-0',
    $colors,
]) }}>
    {{ $slot }}
</div>
