<x-general.loading-state.text :text="$model->balance()" />

<div wire:loading.class="hidden">
    <x-general.amount-fiat-tooltip :amount="$model->balance()" :fiat="$model->balanceFiat()" />
</div>
