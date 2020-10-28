<x-general.loading-state.text :text="$model->transactionCount()" />

<span wire:loading.class="hidden">{{ $model->transactionCount() }}</span>
