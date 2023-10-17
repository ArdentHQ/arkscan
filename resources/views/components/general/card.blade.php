@props(['withBorder' => false])

<div {{ $attributes->class([
        'rounded md:rounded-xl',
        'px-6 py-4 bg-white dark:bg-theme-secondary-900' => ! $withBorder,
        'p-6 border border-theme-secondary-300 dark:border-theme-secondary-800' => $withBorder,
    ]) }}>
    {{ $slot }}
</div>
