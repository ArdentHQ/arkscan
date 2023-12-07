@props([
    'model',
    'wallet' => null,
    'label'  => null,
    'alwaysShowAddress' => false,
    'withoutTruncate' => false,
])

<x-tables.rows.mobile.encapsulated.cell :attributes="$attributes">
    <x-slot name="label">
        @unless ($label)
            <x-general.encapsulated.transaction-type :transaction="$model" />
        @else
            {{ $label }}
        @endif
    </x-slot>

    <x-tables.rows.desktop.encapsulated.addressing
        :model="$model"
        :wallet="$wallet"
        :without-link="$wallet && $model->isSentToSelf($wallet->address())"
        :always-show-address="$alwaysShowAddress"
        :without-truncate="$withoutTruncate"
    />
</x-tables.rows.mobile.encapsulated.cell>
