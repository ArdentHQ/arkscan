<div class="items-center">
    @lang('labels.amount')

    <x-general.amount-fiat-tooltip :amount="$model->amount()" :fiat="$model->amountFiat()" is-sent />
</div>
