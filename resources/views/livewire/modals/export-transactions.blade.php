<div
    wire:init="setIsReady"
    class="flex-1 sm:h-8 export-modal"
    x-data="{ hasBeenOpened: false }"
>
    <div>
        <button
            type="button"
            class="flex justify-center items-center py-1.5 space-x-2 w-full sm:px-4 button-secondary"
            wire:click="openModal"
            @if(!$hasTransactions)
                disabled
            @endif
        >
            <x-ark-icon
                name="arrows.underline-arrow-down"
                size="sm"
            />

            <div>@lang('actions.export')</div>
        </button>
    </div>

    @if($this->modalShown)
        <div
            x-data="TransactionsExport({
                address: '{{ $this->address }}',
                network: {{ json_encode(Network::toArray()) }},
                userCurrency: '{{ Settings::currency() }}',
                rates: {{ ExchangeRate::rates() ?? '{}' }},
                canBeExchanged: {{ Network::canBeExchanged() ? 'true' : 'false' }},
            })"
            x-init="() => {
                resetForm();

                if (! hasBeenOpened) {
                    sa_event('wallet_modal_export_transactions_opened');
                    hasBeenOpened = true;
                }
            }"
        >
            <x-modals.modal :title="trans('pages.wallet.export-transactions-modal.title')">
                <x-slot name="description">
                    <div class="px-6 -mx-6 mt-1 font-normal text-theme-secondary-700 dark:text-theme-dark-200 pt-4 border-t border-theme-secondary-300 dark:border-theme-dark-700">
                        @lang('pages.wallet.export-transactions-modal.description')
                    </div>

                    <div class="px-6 -mx-6 mt-4">
                        <div x-show="! hasStartedExport">
                            <x-modals.export-transactions.fields />
                        </div>

                        <div x-show="hasStartedExport">
                            <x-modals.export.status
                                :partial-download-toast="trans('pages.wallet.export-transactions-modal.success_toast', ['address' => $this->address.'-partial'])"
                            />
                        </div>
                    </div>
                </x-slot>

                <x-slot name="buttons">
                    <x-modals.export.buttons
                        :filename="$this->address"
                        :success-toast="trans('pages.wallet.export-transactions-modal.success_toast', ['address' => $this->address])"
                    />
                </x-slot>
            </x-modals.modal>
        </div>
    @endif
</div>
