<div class="flex items-center justify-between pb-4 border-b border-theme-secondary-300">
    <div class="flex flex-col space-y-2">
        <span class="text-sm font-semibold text-theme-secondary-500">{{ $title }}</span>
        <span class="text-lg font-semibold text-theme-secondary-700">
            <div wire:loading.class="hidden" wire:key="{{ $transaction->id() }}">
                <x-general.address-iconless :model="$model" />
            </div>
        </span>
    </div>

    <div class="flex items-center justify-center p-2">
        <x-general.avatar :identifier="$model->address()" />
    </div>
</div>
