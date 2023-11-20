<div class="flex justify-center items-center space-x-2 w-full text-theme-secondary-700 dark:text-theme-dark-200">
    @if ($model->isKnown())
        <div data-tippy-content="@lang('labels.verified_address')">
            <x-ark-icon name="app-verified" size="sm" />
        </div>
    @endif

    @if ($model->hasMultiSignature())
        <div data-tippy-content="@lang('labels.multi_signature')">
            <x-ark-icon name="app.transactions-multi-signature" size="sm" />
        </div>
    @endif

    @if ($model->isOwnedByExchange())
        <div data-tippy-content="@lang('labels.exchange')">
            <x-ark-icon name="app-exchange" size="sm" />
        </div>
    @endif

    @if ($model->hasSecondSignature())
        <div data-tippy-content="@lang('labels.second_signature')">
            <x-ark-icon name="app.transactions-second-signature" size="sm" />
        </div>
    @endif
</div>
