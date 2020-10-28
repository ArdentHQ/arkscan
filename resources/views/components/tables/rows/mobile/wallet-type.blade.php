@if ($model->isKnown() || $model->isOwnedByExchange())
    <div class="flex justify-between w-full">
        @lang('labels.wallet_type')

        <div class="flex flex-col space-y-4">
            @if ($model->isKnown())
                <div>
                    <div class="flex items-center space-x-4">
                        <x-general.loading-state.icon icon="app-verified" />
                        <x-general.loading-state.text :text="trans('general.verified_address')" />
                    </div>

                    <div class="flex items-center space-x-4" wire:loading.class="hidden">
                        @svg('app-verified', 'w-5 h-5 text-theme-secondary-500')

                        <span>@lang('general.verified_address')</span>
                    </div>
                </div>
            @endif

            @if ($model->isOwnedByExchange())
                <div>
                    <div class="flex items-center space-x-4">
                        <x-general.loading-state.icon icon="app-exchange" />
                        <x-general.loading-state.text :text="trans('general.exchange')" />
                    </div>

                    <div class="flex items-center space-x-4" wire:loading.class="hidden">
                        @svg('app-exchange', 'w-5 h-5 text-theme-secondary-500')

                        <span>@lang('general.exchange')</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
