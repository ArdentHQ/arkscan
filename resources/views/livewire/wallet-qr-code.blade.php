<div>
    <div class="flex justify-between items-center text-lg font-semibold text-left dark:text-theme-dark-50 px-6 py-[0.875rem]">
        <div class="text-lg text-theme-secondary-900 dark:text-theme-dark-200">
            @lang('pages.wallet.qrcode.title')
        </div>

        {{-- close-button-class="absolute top-0 right-0 p-0 mt-4 mr-6 w-6 h-6 bg-transparent rounded-none sm:mt-[0.875rem] sm:rounded dark:bg-transparent dark:shadow-none button button-secondary text-theme-secondary-700 dim:bg-transparent dim:shadow-none dark:text-theme-dark-200 hover:dark:text-theme-dark-50 hover:dark:bg-theme-dark-blue-600"
        title-class="mb-[0.875rem] text-lg font-semibold text-left dark:text-theme-dark-50"
        padding-class="px-6 py-4 sm:pt-[0.875rem] sm:pb-4"
        buttons-style="flex flex-col-reverse sm:flex-row sm:justify-end !mt-4 sm:!mt-6 sm:space-x-3 border-t border-theme-secondary-300 dark:border-theme-dark-700 px-6 -mx-6 pt-4" --}}
        <div>
            <button
                type="button"
                class="flex w-6 h-6 p-0 items-center justify-center hover:text-white button button-generic dark:hover:text-white dark:text-theme-dark-600 hover:bg-theme-primary-700"
                @click="dropdownOpen = false"
            >
                <x-ark-icon
                    name="cross"
                    size="xs"
                />
            </button>
        </div>
    </div>

    <div class="px-6 pb-6 pt-[0.875rem] border-t border-theme-secondary-300 dark:border-theme-dark-700">
        <div
            x-show="showOptions"
            class="font-normal text-theme-secondary-700 leading-5.25 dark:text-theme-dark-200"
        >
            @lang('pages.wallet.qrcode.description')
        </div>

        <div
            x-show="showOptions"
            class="py-4"
        >
            <x-ark-input
                type="number"
                id="amount"
                name="amount"
                maxlength="17"
                class="font-normal"
                input-class="qr-code-amount"
                :errors="$errors"
                :label="trans('pages.wallet.qrcode.currency_amount', ['currency' => Network::currency()])"
                x-on:wheel="preventAmountScroll"
                autofocus
            />
        </div>

        <div class="flex flex-col items-center">
            <div class="inline-block p-2 bg-white rounded-lg border sm:block border-theme-secondary-300 dark:border-theme-dark-300">
                {!! $this->code !!}
            </div>

            <button
                x-show="! showOptions"
                class="mt-3 w-full button-secondary"
                @click="showOptions = true"
            >
                @lang('pages.wallet.qrcode.specify_amount')
            </button>

            <div
                x-show="showOptions"
                class="mt-4 font-normal text-theme-secondary-700 leading-5.25 dark:text-theme-dark-200"
            >
                @lang('pages.wallet.qrcode.automatic_notice')
            </div>

            <div class="flex items-center mt-3 space-x-3 w-full">
                <div class="flex-1 border-t h-1px border-theme-secondary-300 dark:border-theme-dark-700"></div>

                <div class="font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
                    @lang('pages.wallet.qrcode.or_send_with')
                </div>

                <div class="flex-1 border-t h-1px border-theme-secondary-300 dark:border-theme-dark-700"></div>
            </div>

            <div class="mt-2 w-full">
                @php ($arkconnectEnabled = config('arkscan.arkconnect.enabled'))

                @if ($arkconnectEnabled)
                    <div class="flex flex-col w-full">
                        <div
                            x-show="isOnSameNetwork"
                            wire:key="arkconnect:amount:{{ $this->hasAmount ? 'enabled' : 'disabled' }}"
                            @if (! $this->hasAmount)
                                data-tippy-content="@lang('pages.wallet.qrcode.arkconnect_specify_amount_tooltip')"
                            @endif
                        >
                            <button
                                type="button"
                                class="w-full button-primary"
                                x-on:click="await performSend('{{ $this->address }}', '{{ $this->amount }}')"
                                @if (! $this->hasAmount)
                                    disabled
                                @endif
                            >
                                @lang('brands.arkconnect')
                            </button>
                        </div>

                        <x-arkconnect.disabled-action>
                            <button
                                type="button"
                                class="w-full button-primary"
                                disabled
                            >
                                @lang('brands.arkconnect')
                            </button>
                        </x-arkconnect.disabled-action>

                        <x-ark-external-link
                            :url="$this->walletUri"
                            class="mt-2 w-full button-secondary"
                            icon-class="inline relative -top-1 flex-shrink-0 mt-1 ml-0.5 text-theme-primary-400 dim:text-theme-dim-blue-300 dark:text-theme-dark-500"
                        >
                            @lang('brands.arkvault')
                        </x-ark-external-link>
                    </div>
                @else
                    <div>
                        <x-ark-external-link
                            :url="$this->walletUri"
                            class="w-full button-primary"
                            icon-class="inline relative -top-1 flex-shrink-0 mt-1 ml-0.5 text-theme-primary-400 dim:text-theme-dim-blue-300 dark:text-theme-dark-blue-300"
                        >
                            @lang('brands.arkvault')
                        </x-ark-external-link>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
