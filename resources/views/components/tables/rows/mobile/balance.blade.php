<div>
    <span class="font-semibold">
        @lang('labels.balance')
    </span>

    <x-general.amount-fiat-tooltip :amount="$model->balance()" :fiat="$model->balanceFiat()" />
</div>
