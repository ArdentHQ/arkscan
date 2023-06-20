@props([
    'model',
    'class' => null,
    'withoutStyling' => false,
])

<x-general.encapsulated.amount-fiat-tooltip
    :amount="$model->reward()"
    :fiat="$model->rewardFiat(true)"
    :class="$class"
    :without-styling="$withoutStyling"
/>
