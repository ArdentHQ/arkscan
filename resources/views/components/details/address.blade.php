<div class="flex items-center justify-between pb-4 border-b border-theme-secondary-300 dark:border-theme-secondary-800">
    <div class="flex flex-col space-y-2">
        <span class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">{{ $title }}</span>
        <span class="font-semibold text-theme-secondary-700">
            <div wire:key="{{ $transaction->id() }}">
                @if($model->isDelegate())
                    <x-general.identity-delegate :model="$model" />
                @else
                    <span class="inline lg:hidden">
                        <x-general.identity-iconless :model="$model" :length="16" />
                    </span>
                    <span class="hidden lg:inline">
                        <x-general.identity-iconless :model="$model" :length="32" />
                    </span>
                @endif
            </div>
        </span>
    </div>

    <div class="flex items-center justify-center w-12 h-12">
        <x-general.avatar :identifier="$model->address()" avatar-size="w-11 h-11"/>
    </div>
</div>
