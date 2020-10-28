<x-general.loading-state.text :text="$model->fee()" />

<div wire:loading.class="hidden">
    <x-general.amount-fiat-tooltip :amount="$model->fee()" :fiat="$model->feeFiat()" />
</div>
