@props(['model'])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center') }}>
    <div class="text-sm font-semibold leading-[17px] dark:text-theme-secondary-500">
        <x-general.encapsulated.transaction-type :transaction="$model" />
    </div>

    <x-tables.rows.desktop.encapsulated.addressing-generic :model="$model" />
</div>