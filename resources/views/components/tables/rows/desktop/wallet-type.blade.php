<div class="flex justify-center items-center space-x-2 w-full text-theme-secondary-700 dark:text-theme-dark-200">
    @if ($model->isKnown())
        <div data-tippy-content="@lang('labels.verified_address')">
            <x-ark-icon name="app-verified" size="sm" />
        </div>
    @endif

    @if ($model->isOwnedByExchange())
        <div data-tippy-content="@lang('labels.exchange')">
            <x-ark-icon name="app-exchange" size="sm" />
        </div>
    @endif
</div>
