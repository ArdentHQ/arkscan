@props([
    'model',
    'class' => null,
    'withoutStyling' => false,
])

<x-general.encapsulated.amount-fiat-tooltip
    :amount="$model->totalReward()"
    :fiat="$model->totalRewardFiat(true)"
    :class="$class"
    :without-styling="$withoutStyling"
/>
