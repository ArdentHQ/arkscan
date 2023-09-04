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
    />

    @unless ($withoutValue)
        <div class="xl:hidden text-xs font-semibold mt-1 leading-4.25">
            {{ $model->rewardFiat() }}
        </div>
    @endunless
</div>
