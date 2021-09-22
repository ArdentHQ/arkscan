<div>
    <div class="flex {{ ($withoutReverse ?? false) ? 'space-x-3' : 'flex-row-reverse' }} items-center sm:justify-start">
        <div class="hidden sm:block">
            <x-general.avatar :identifier="$model->address()" no-shrink />
        </div>

        <div class="flex flex-col flex-1 justify-center mr-4 sm:mr-0">
            <div class="text-sm font-semibold text-theme-secondary-500">@lang('general.address')</div>

            <div class="flex justify-between items-center sm:justify-start">
                <div>
                    <div class="hidden font-semibold sm:flex text-theme-secondary-900 dark:text-theme-secondary-200">
                        <x-truncate-middle :length="21">
                            {{ $model->address() }}
                        </x-truncate-middle>
                    </div>

                    <div class="font-semibold sm:hidden text-theme-secondary-900 dark:text-theme-secondary-200">
                        <x-truncate-middle :length="17">
                            {{ $model->address() }}
                        </x-truncate-middle>
                    </div>
                </div>

                <x-clipboard :value="$model->address()" />
            </div>
        </div>
    </div>
</div>
