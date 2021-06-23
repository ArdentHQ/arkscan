<div class="flex items-center">
    <div class="flex justify-center items-center p-2">
        <div class="flex justify-center items-center -mr-2 w-12 h-12">
            <div class="circled-icon text-theme-secondary-900 border-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-600">
                @if($transaction->isSent($wallet->address()))
                    <x-ark-icon name="app-arrow-up" />
                @else
                    <x-ark-icon name="app-arrow-down" />
                @endif
            </div>
        </div>

        <x-general.avatar :identifier="$transaction->sender()->address()" no-shrink />
    </div>

    <div class="flex flex-col space-y-2">
        <span class="font-semibold text-theme-secondary-700">
            <x-general.identity-iconless :model="$transaction->sender()" />
        </span>
    </div>
</div>
