<div
    wire:init="setIsReady"
    class="flex-1 sm:h-8 export-modal"
>
    <div @class(['cursor-not-allowed' => !$hasForgedBlocks])>
        <button
            type="button"
            class="flex justify-center items-center py-1.5 space-x-2 w-full sm:px-4 button-secondary"
            wire:click="openModal"
            @if(!$hasForgedBlocks)
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
            x-data="BlocksExport({
                publicKey: '{{ $this->publicKey }}',
                network: {{ json_encode(Network::toArray()) }},
                userCurrency: '{{ Settings::currency() }}',
                rates: {{ ExchangeRate::rates() ?? '{}' }},
                canBeExchanged: {{ Network::canBeExchanged() ? 'true' : 'false' }},
            })"
            x-init="resetForm"
        >
            <x-ark-modal
                title-class="mb-6 text-lg font-semibold text-left dark:text-theme-dark-50"
                padding-class="p-6 py-4 sm:py-6"
                wire-close="closeModal"
                close-button-class="absolute top-0 right-0 p-0 mt-4 mr-6 w-8 h-8 bg-transparent rounded-none sm:mt-6 sm:rounded dark:bg-transparent dark:shadow-none button button-secondary text-theme-secondary-700 dark:text-theme-dark-200 hover:dark:text-theme-dark-50 hover:dark:bg-theme-dark-blue-600"
                buttons-style="flex flex-col-reverse sm:flex-row sm:justify-end !mt-4 sm:!mt-6 sm:space-x-3"
                breakpoint="sm"
                wrapper-class="max-w-full sm:max-w-[448px]"
                content-class="relative bg-white sm:mx-auto sm:rounded-xl sm:shadow-2xl dark:bg-theme-dark-900"
                disable-overlay-close
            >
                <x-slot name="title">
                    <div>@lang('pages.wallet.export-blocks-modal.title')</div>

                    <div class="mt-1 text-sm font-normal text-theme-secondary-700 dark:text-theme-dark-200">
                        @lang('pages.wallet.export-blocks-modal.description')
                    </div>
                </x-slot>

                <x-slot name="description">
                    <div class="px-6 pt-6 -mx-6 border-t border-theme-secondary-300 dark:border-theme-dark-700">
                        <div x-show="! hasStartedExport">
                            <x-modals.export-blocks.fields />
                        </div>

                        <div x-show="hasStartedExport">
                            <x-modals.export.status
                                :filename="$this->username"
                                type="blocks"
                                :partial-download-toast="trans('pages.wallet.export-blocks-modal.success_toast', ['username' => $this->username.'-partial'])"
                            />
                        </div>
                    </div>
                </x-slot>

                <x-slot name="buttons">
                    <x-modals.export.buttons
                        :filename="$this->username"
                        :success-toast="trans('pages.wallet.export-blocks-modal.success_toast', ['username' => $this->username])"
                    />
                </x-slot>
            </x-ark-modal>
        </div>
    @endif
</div>
