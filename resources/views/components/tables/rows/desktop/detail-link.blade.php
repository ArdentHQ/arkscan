<div class="flex items-center">
    <x-general.loading-state.icon icon="link" class="mx-auto" />

    <a href="{{ $model->url() }}" class="mx-auto link" wire:loading.class="hidden">
        @svg('app-details', 'h-4 w-4')
    </a>
</div>
