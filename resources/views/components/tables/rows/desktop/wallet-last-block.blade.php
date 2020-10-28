<div wire:loading.class="h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
@if($model->lastBlock())
    <a href="{{ route('block', $model->lastBlock()['id']) }}" class="font-semibold link" wire:loading.class="hidden">
        <x-truncate-middle :value="$model->lastBlock()['id']" />
    </a>
@else
    <div wire:loading.class="hidden">n/a</div>
@endif
