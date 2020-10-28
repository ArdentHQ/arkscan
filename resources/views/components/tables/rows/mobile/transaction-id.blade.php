<div>
    @lang('labels.transaction_id')

    <x-general.loading-state.text class="font-semibold">
        <x-slot name="text">
            <x-truncate-middle :value="$model->id()" />
        </x-slot>
    </x-general.loading-state.text>

    <a href="{{ $model->url() }}" class="font-semibold link" wire:loading.class="hidden">
        <x-truncate-middle :value="$model->id()" />
    </a>
</div>
