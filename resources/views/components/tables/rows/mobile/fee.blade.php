<div>
    <span class="font-semibold">
        @lang('labels.fee')
    </span>

    <x-general.amount-fiat-tooltip :amount="$model->fee()" :fiat="$model->feeFiat()" />
</div>
