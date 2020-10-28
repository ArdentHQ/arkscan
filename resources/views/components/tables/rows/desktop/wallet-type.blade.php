<div class="flex items-center justify-center space-x-2 text-theme-secondary-500">
    @if ($model->isKnown())
        <x-general.loading-state.icon icon="app-verified" />

        <div data-tippy-content="@lang('labels.verified_address')"  wire:loading.class="hidden">
            @svg('app-verified', 'w-5 h-5')
        </div>
    @endif

    @if ($model->isOwnedByExchange())
        <x-general.loading-state.icon icon="app-exchange" />

        <div data-tippy-content="@lang('labels.exchange')"  wire:loading.class="hidden">
            @svg('app-exchange', 'w-5 h-5')
        </div>
    @endif
</div>
