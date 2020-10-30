<div class="flex items-center justify-between pb-4 border-b border-theme-secondary-300">
    <div class="flex flex-col space-y-2">
        <span class="text-sm font-semibold text-theme-secondary-500">{{ $title }}</span>
        <span class="font-semibold text-theme-secondary-700">
            <div wire:key="{{ $transaction->id() }}">
                <x-general.identity-iconless :model="$model" />
            </div>
        </span>
    </div>

    <div class="flex items-center justify-center p-2">
        <div class="flex items-center justify-center w-12 h-12 -mr-2">
            {!! $icon !!}
        </div>

        <x-general.avatar :identifier="$model->address()" />
    </div>
</div>
