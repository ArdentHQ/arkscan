<div class="flex items-center space-x-4">
    <div class="flex justify-center items-center sender-direction-wrapper">
        <div class="flex">
            @if($model->isSent($wallet->address()))
                <div class="arrow-direction text-theme-danger-400 border-theme-danger-100 dark:border-theme-danger-400">
                    <x-ark-icon name="sent" />
                </div>
            @else
                <div class="arrow-direction text-theme-success-600 border-theme-success-200 dark:border-theme-success-600">
                    <x-ark-icon name="received" />
                </div>
            @endif

            <div class="table-avatar">
                <div class="dark:text-theme-secondary-600 dark:border-theme-secondary-600">
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
