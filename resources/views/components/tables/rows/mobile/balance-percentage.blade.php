<div>
    @lang('labels.balance_percentage')

    <x-general.loading-state.text :text="$model->balancePercentage()" />

    <div wire:loading.class="hidden">
        {{ $model->balancePercentage() }}
    </div>
</div>
