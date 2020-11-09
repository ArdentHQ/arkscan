<div class="flex items-center" wire:key="sender:{{ $model->id() }}">
    <div class="flex items-center justify-center p-2">
        <div class="flex">
            <div class="-mr-2 circled-icon text-theme-secondary-900 border-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-600">
                @if($model->isSent($wallet->address()))
                    <x-ark-icon name="app-arrow-up" />
                @else
                    <x-ark-icon name="app-arrow-down" />
                @endif
            </div>

            <div class="-mt-1 border-4 border-white rounded-full dark:border-theme-secondary-900">
                <div class="bg-white border-white circled-icon dark:text-theme-secondary-600 dark:border-theme-secondary-600">
                    <x-general.avatar :identifier="$model->sender()->address()" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col space-y-2">
        <span class="font-semibold text-theme-secondary-700">
            <x-general.identity-iconless :model="$model->sender()" />
        </span>
    </div>
</div>
