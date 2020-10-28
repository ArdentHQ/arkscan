<x-general.loading-state.text :text="$model->height()" />

<span wire:loading.class="hidden">{{ $model->height() }}<span>
