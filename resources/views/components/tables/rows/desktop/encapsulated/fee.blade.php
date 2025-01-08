@props([
    'model',
    'class' => null,
    'withoutStyling' => false,
])

<x-general.encapsulated.amount-fiat-tooltip
    :amount="$model->fee()"
    :fiat="$model->feeFiat(true)"
    :class="$class"
    :without-styling="$withoutStyling"
    :transaction="$model"
/>
