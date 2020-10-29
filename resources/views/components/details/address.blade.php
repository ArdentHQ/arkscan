<div class="flex items-center justify-between pb-4 border-b border-theme-secondary-300 dark:border-theme-secondary-800">
    <div class="flex flex-col space-y-2">
        <span class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">{{ $title }}</span>
        <span class="text-lg font-semibold text-theme-secondary-700">
            <div wire:key="{{ $transaction->id() }}">
                @if($model->isDelegate())
                    <x-general.identity-delegate :model="$model" />
                @else
                    <x-general.identity-iconless :model="$model" without-truncate />
                @endif
            </div>
        </span>
    </div>

    <div class="flex items-center justify-center p-2">
        <x-general.avatar :identifier="$model->address()" />
    </div>
</div>
