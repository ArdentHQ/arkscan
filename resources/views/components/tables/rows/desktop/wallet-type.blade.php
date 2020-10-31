<div class="flex items-center justify-center space-x-2 text-theme-secondary-500">
    @if ($model->isKnown())
        <div data-tippy-content="@lang('labels.verified_address')">
            <x-icon name="app-verified" />
        </div>
    @endif

    @if ($model->isOwnedByExchange())
        <div data-tippy-content="@lang('labels.exchange')">
            <x-icon name="app-exchange" />
        </div>
    @endif
</div>
