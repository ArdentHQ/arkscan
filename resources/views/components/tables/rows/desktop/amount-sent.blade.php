@props([
    'model',
    'excludeItself' => false,
])

@if($excludeItself && $model->isMultiPayment())
    <x-general.amount-fiat-tooltip
        :amount="$model->amountExcludingItSelf()"
        :fiat="$model->amountFiatExcludingItSelf()"
        :amount-for-itself="$model->amountForItSelf()"
        is-sent
    />
@else
    <x-general.amount-fiat-tooltip :amount="$model->amount()" :fiat="$model->amountFiat()" is-sent />
@endif

