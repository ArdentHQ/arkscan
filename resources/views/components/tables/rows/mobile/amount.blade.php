<div class="items-center">
    @lang('labels.amount')

    <x-general.amount-fiat-tooltip>
        <x-slot name="amount">
            <x-currency :currency="Network::currency()">{{ $model->amount() }}</x-currency>
        </x-slot>

        <x-slot name="fiat">
            {{ $model->amountFiat() }}
        </x-slot>
    </x-general.amount-fiat-tooltip>
</div>
