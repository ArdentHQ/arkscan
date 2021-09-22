<div>
    @lang('labels.balance')

    <x-general.amount-fiat-tooltip :amount="$model->balance()" :fiat="$model->balanceFiat()" />
</div>
