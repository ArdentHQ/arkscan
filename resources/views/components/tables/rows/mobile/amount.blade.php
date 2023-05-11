<div class="items-center">
    <span class="font-semibold">
        @lang('labels.amount')
    </span>

    <x-general.amount-fiat-tooltip>
        <x-slot name="amount">
            {{ $model->amount() }}
        </x-slot>

        <x-slot name="fiat">
            {{ $model->amountFiat() }}
        </x-slot>
    </x-general.amount-fiat-tooltip>
</div>
