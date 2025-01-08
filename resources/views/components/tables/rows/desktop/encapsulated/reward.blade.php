@props([
    'model',
    'class' => null,
    'withoutStyling' => false,
    'withoutValue' => true,
])

<div class="flex flex-col">
    <x-general.encapsulated.amount-fiat-tooltip
        :amount="$model->totalReward()"
        :fiat="$model->totalRewardFiat(true)"
        :class="$class"
        :without-styling="$withoutStyling"
        :block="$model"
    />

    @unless ($withoutValue)
        <div class="mt-1 text-xs font-semibold xl:hidden leading-4.25">
            {{ $model->rewardFiat() }}
        </div>
    @endunless
</div>
