<div>
    @lang('labels.balance')

    <span class="text-theme-secondary-900 dark:text-theme-secondary-200 font-semibold">
        <x-general.amount-fiat-tooltip :amount="$model->balance()" :fiat="$model->balanceFiat()" />
    </span>
</div>
