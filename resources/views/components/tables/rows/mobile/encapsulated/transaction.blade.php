@props([
    'model',
    'wallet' => null,
])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center') }}>
    <div class="text-sm font-semibold dark:text-theme-secondary-500 leading-[17px]">
        <x-general.encapsulated.transaction-type :transaction="$model" />
    </div>

    <x-tables.rows.desktop.encapsulated.addressing
        :model="$model"
        :wallet="$wallet"
    />
</div>
