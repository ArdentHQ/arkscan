<x-general.loading-state.text :text="$model->balancePercentage()" />

<div wire:loading.class="hidden">
    {{ $model->balancePercentage() }}
</div>
