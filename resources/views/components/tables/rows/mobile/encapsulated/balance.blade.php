<div>
    <span class="font-semibold">
        @lang('labels.balance')
    </span>

    <span class="font-semibold text-theme-secondary-900 dark:text-theme-secondary-200">
        <x-general.amount-fiat-tooltip :amount="$model->balance()" :fiat="$model->balanceFiat()" />
    </span>
</div>
