@props([
    'model',
    'wallet' => null,
    'label'  => null,
    'alwaysShowAddress' => false,
    'withoutTruncate' => false,
])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center') }}>
    <div class="text-sm font-semibold leading-4.25 dark:text-theme-dark-500">
        @unless ($label)
            <x-general.encapsulated.transaction-type :transaction="$model" />
        @else
            {{ $label }}
        @endif
    </div>

    <x-tables.rows.desktop.encapsulated.addressing
        :model="$model"
        :wallet="$wallet"
        :without-link="$wallet && $model->isSentToSelf($wallet->address())"
        :always-show-address="$alwaysShowAddress"
        :without-truncate="$withoutTruncate"
    />
</div>
