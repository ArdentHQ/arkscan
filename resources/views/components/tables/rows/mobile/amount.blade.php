<div class="flex justify-between w-full">
    @lang('labels.amount')

    <x-general.loading-state.text :text="$model->amount()" />

    <div wire:loading.class="hidden">
        <x-general.amount-fiat-tooltip :amount="$model->amount()" :fiat="$model->amountFiat()" />
    </div>
</div>
