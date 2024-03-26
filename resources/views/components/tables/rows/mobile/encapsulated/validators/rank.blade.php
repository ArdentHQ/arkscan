@props(['model'])

<div {{ $attributes->class('font-semibold dark:text-theme-dark-200') }}>
    <x-tables.rows.mobile.rank :model="$model" />
</div>
