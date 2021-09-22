<div class="flex justify-between items-center pb-4 border-b border-theme-secondary-300 dark:border-theme-secondary-800">
    <div class="flex flex-col flex-1 space-y-2 min-w-0">
        <div class="flex items-center">
            <span class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">
                {{ $title }}
            </span>
            @if ($titleIcon ?? false)
                <x-ark-icon :name="$titleIcon" size="sm" class="ml-2 text-theme-secondary-500 dark:text-theme-secondary-700" />
            @endif
        </div>
        <span class="font-semibold text-theme-secondary-700">
            <div wire:key="{{ $transaction->id() }}">
                @if($model->isDelegate())
                    <x-general.identity-delegate :model="$model" />
                @else
                    <x-general.identity-iconless :model="$model" dynamic-truncate />
                @endif
            </div>
        </span>
    </div>

    <div class="flex justify-center items-center w-12 h-12">
        <x-general.avatar :identifier="$model->address()" no-shrink />
    </div>
</div>
