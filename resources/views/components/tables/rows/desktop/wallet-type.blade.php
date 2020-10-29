<div class="flex items-center justify-center space-x-2 text-theme-secondary-500">
    @if ($model->isKnown())
        <div data-tippy-content="@lang('labels.verified_address')">
            @svg('app-verified', 'w-5 h-5')
        </div>
    @endif

    @if ($model->isOwnedByExchange())
        <div data-tippy-content="@lang('labels.exchange')">
            @svg('app-exchange', 'w-5 h-5')
        </div>
    @endif
</div>
