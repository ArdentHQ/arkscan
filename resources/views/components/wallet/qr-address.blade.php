<div>
    <div class="flex {{ ($withoutReverse ?? false) ? 'space-x-3' : 'flex-row-reverse' }} items-center sm:justify-start">
        <div class="hidden sm:block">
            <x-general.avatar :identifier="$model->address()" no-shrink />
        </div>

        <div class="flex flex-col justify-center flex-1 mr-4 sm:mr-0">
            <div class="text-sm font-semibold text-theme-secondary-500">@lang('general.address')</div>

            <div class="flex items-center justify-between sm:justify-start">
                <div>
                    <div class="hidden font-semibold text-theme-secondary-900 dark:text-theme-secondary-200 sm:flex">
                        <x-truncate-middle :length="19">
                            {{ $model->address() }}
                        </x-truncate-middle>
                    </div>

                    <div class="font-semibold text-theme-secondary-900 dark:text-theme-secondary-200 sm:hidden">
                        <x-truncate-middle :length="15">
                            {{ $model->address() }}
                        </x-truncate-middle>
                    </div>
                </div>

                <x-clipboard :value="$model->address()" />
            </div>
        </div>
    </div>
</div>
