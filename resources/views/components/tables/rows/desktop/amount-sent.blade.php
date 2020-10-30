<x-general.amount-fiat-tooltip is-sent>
    <x-slot name="amount">
        <x-currency>{{ $model->amount() }}</x-currency>
    </x-slot>

    <x-slot name="fiat">
        {{ $model->amountFiat() }}
    </x-slot>
</x-general.amount-fiat-tooltip>
