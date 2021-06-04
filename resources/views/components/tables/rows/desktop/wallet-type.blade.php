<div class="flex justify-center items-center space-x-2 w-full text-theme-secondary-900 dark:text-theme-secondary-500">
    @if ($model->isKnown())
        <div data-tippy-content="@lang('labels.verified_address')">
            <x-ark-icon name="app-verified" />
        </div>
    @endif

    @if ($model->hasMultiSignature())
        <div data-tippy-content="@lang('labels.multi_signature')">
            <x-ark-icon name="app.transactions-multi-signature" />
        </div>
    @endif

    @if ($model->isOwnedByExchange())
        <div data-tippy-content="@lang('labels.exchange')">
            <x-ark-icon name="app-exchange" />
        </div>
    @endif

    @if ($model->hasSecondSignature())
        <div data-tippy-content="@lang('labels.second_signature')">
            <x-ark-icon name="app.transactions-second-signature" />
        </div>
    @endif
</div>
