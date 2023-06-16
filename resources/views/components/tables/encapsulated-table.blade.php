@props([
    'rounded' => true,
])

<div {{ $attributes->class([
    'border border-theme-secondary-300 dark:border-theme-secondary-800 table-container px-6 table-encapsulated encapsulated-table-header-gradient',
    'rounded-t-xl' => $rounded,
]) }}>
    <table>
        {{ $slot }}
    </table>
</div>
