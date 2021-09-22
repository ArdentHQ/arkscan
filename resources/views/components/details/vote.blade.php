<div class="flex justify-between items-center pb-4 border-b border-theme-secondary-300 dark:border-theme-secondary-800">
    <div class="flex flex-col space-y-2">
        <span class="text-sm font-semibold text-theme-secondary-500">{{ $title }}</span>
        <span class="font-semibold text-theme-secondary-700">
            <div wire:key="{{ $transaction->id() }}">
                <x-general.identity-iconless :model="$model" />
            </div>
        </span>
    </div>

    <div class="flex justify-center items-center p-2">
        <div class="flex justify-center items-center -mr-2 w-12 h-12">
            {!! $icon !!}
        </div>

        <x-general.avatar :identifier="$model->address()" no-shrink />
    </div>
</div>
