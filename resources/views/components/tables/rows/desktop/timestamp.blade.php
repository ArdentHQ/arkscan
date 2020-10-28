<x-general.loading-state.text :text="$model->timestamp()" />

<span wire:loading.class="hidden">{{ $model->timestamp() }}</span>
