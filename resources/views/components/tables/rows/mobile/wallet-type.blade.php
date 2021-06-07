@if ($model->hasSpecialType())
    <div>
        @lang('labels.wallet_type')

        <div class="flex flex-col items-end space-y-4">
            @if ($model->isKnown())
                <div>
                    <div class="flex items-center space-x-4">
                        <span>@lang('general.verified_address')</span>

                        <x-ark-icon
                            name="app-verified"
                            class="text-theme-secondary-900"
                        />
                    </div>
                </div>
            @endif

            @if ($model->hasMultiSignature())
                <div>
                    <div class="flex items-center space-x-4">
                        <span>@lang('labels.multi_signature')</span>

                        <x-ark-icon
                            name="app.transactions-multi-signature"
                            class="text-theme-secondary-900"
                        />
                    </div>
                </div>
            @endif

            @if ($model->isOwnedByExchange())
                <div>
                    <div class="flex items-center space-x-4">
                        <span>@lang('labels.exchange')</span>

                        <x-ark-icon
                            name="app-exchange"
                            class="text-theme-secondary-900"
                        />
                    </div>
                </div>
            @endif

            @if ($model->hasSecondSignature())
                <div>
                    <div class="flex items-center space-x-4">
                        <span>@lang('labels.second_signature')</span>

                        <x-ark-icon
                            name="app.transactions-second-signature"
                            class="text-theme-secondary-900"
                        />
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
