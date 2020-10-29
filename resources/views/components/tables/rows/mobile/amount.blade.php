<div>
    @lang('labels.amount')

    <x-general.amount-fiat-tooltip :amount="$model->amount()" :fiat="$model->amountFiat()" />
</div>
