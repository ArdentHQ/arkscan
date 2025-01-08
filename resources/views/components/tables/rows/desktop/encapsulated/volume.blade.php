@props([
    'model',
    'class' => null,
    'withoutStyling' => false,
])

<x-general.encapsulated.amount-fiat-tooltip
    :amount="$model->amount()"
    :fiat="$model->amountFiat(true)"
    :class="$class"
    :without-styling="$withoutStyling"
    :block="$model"
/>
