<div
    x-data="{
        showOptions: false,
        preventAmountScroll: (e) => {
            const hasFocus = document.activeElement === e.target;
            e.target.blur();
            e.stopPropagation();
            if (hasFocus) {
                setTimeout(() => {
                    e.target.focus()
                }, 0);
            }
        }
    }"
    @class($class)
>
    <x-general.dropdown.dropdown
        width="w-full sm:max-w-[320px] px-4"
        active-button-class=""
        dropdown-padding=""
        dropdown-wrapper-class="w-full"
        dropdown-background="bg-white border border-transparent dark:shadow-lg-dark dark:bg-theme-dark-900 dark:border-theme-dark-800"
        :close-on-click="false"
        on-close="() => showOptions = false"
        button-wrapper-class=""
        button-class="p-2 w-full focus-visible:ring-inset button button-secondary button-icon"
    >
        <x-slot
            name="button"
            wire:click="toggleQrCode"
        >
            <div>
                <x-ark-icon name="qr-code" size="sm" />
            </div>
        </x-slot>

        <div class="flex justify-between items-center p-6 border-b border-theme-secondary-300 dark:border-theme-dark-800">
            <div class="text-lg text-theme-secondary-900 dark:text-theme-dark-200">
                @lang('pages.wallet.qrcode.title')
            </div>

            <div>
                <button
                    type="button"
                    class="p-2 hover:text-white button button-generic dark:hover:text-white dark:text-theme-dark-600 hover:bg-theme-primary-700"
                    @click="dropdownOpen = false"
                >
                    <x-ark-icon
                        name="cross"
                        size="sm"
                    />
                </button>
            </div>
        </div>

        <div class="p-6">
            <div
                x-show="showOptions"
                class="font-normal text-theme-secondary-700 leading-5.25 dark:text-theme-dark-200"
            >
                @lang('pages.wallet.qrcode.description')
            </div>

            <div
                x-show="showOptions"
                class="pt-2 pb-4 space-y-3"
            >
                <x-ark-input
                    type="number"
                    id="amount"
                    name="amount"
                    maxlength="17"
                    class="font-normal"
                    input-class="qr-code-amount"
                    :errors="$errors"
                    :placeholder="trans('pages.wallet.qrcode.currency_amount', ['currency' => Network::currency()])"
                    x-on:wheel="preventAmountScroll"
                    hide-label
                    autofocus
                />

                <x-ark-textarea
                    id="smartbridge"
                    name="smartbridge"
                    maxlength="255"
                    rows="4"
                    class="font-normal"
                    :placeholder="trans('pages.wallet.qrcode.memo_optional')"
                    :errors="$errors"
                    hide-label
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
                    class="mt-4 mb-1 font-normal text-theme-secondary-700 leading-5.25 dark:text-theme-dark-200"
                >
                    @lang('pages.wallet.qrcode.automatic_notice')
                </div>

                <div class="flex items-center mt-2 space-x-3 w-full">
                    <div class="flex-1 border-t h-1px border-theme-secondary-300 dark:border-theme-dark-700"></div>

                    <div class="font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
                        @lang('pages.wallet.qrcode.or_send_with')
                    </div>

                    <div class="flex-1 border-t h-1px border-theme-secondary-300 dark:border-theme-dark-700"></div>
                </div>

                <div class="mt-2 w-full">
                    @php ($arkconnectEnabled = config('arkscan.arkconnect.enabled'))

                    @if ($arkconnectEnabled)
                        <div
                            x-show="showOptions"
                            class="flex flex-col w-full"
                        >
                            <div
                                x-show="isOnSameNetwork"
                                @if (! $this->hasAmount)
                                    data-tippy-content="@lang('pages.wallet.qrcode.arkconnect_specify_amount_tooltip')"
                                @endif
                            >
                                <button
                                    type="button"
                                    class="w-full button-primary"
                                    x-on:click="await performSend('{{ $this->address }}', '{{ $this->amount }}', '{{ $this->smartbridge }}')"
                                    @if (! $this->hasAmount)
                                        disabled
                                    @endif
                                >
                                    @lang('brands.arkconnect')
                                </button>
                            </div>

                            <div
                                x-show="!isOnSameNetwork"
                                data-tippy-content="@lang('general.arkconnect.wrong_network')"
                            >
                                <button
                                    type="button"
                                    class="w-full button-primary"
                                    disabled
                                >
                                    @lang('brands.arkconnect')
                                </button>
                            </div>

                            <x-ark-external-link
                                :url="$this->walletUri"
                                class="mt-2 w-full button-secondary"
                                icon-class="inline relative -top-1 flex-shrink-0 mt-1 ml-0.5 text-theme-primary-400 dim:text-theme-dim-blue-300 dark:text-theme-dark-500"
                            >
                                @lang('brands.arkvault')
                            </x-ark-external-link>
                        </div>
                    @endif

                    <div
                        @if ($arkconnectEnabled)
                            x-show="! showOptions"
                        @endif
                    >
                        <x-ark-external-link
                            :url="$this->walletUri"
                            class="w-full button-primary"
                            icon-class="inline relative -top-1 flex-shrink-0 mt-1 ml-0.5 text-theme-primary-400 dim:text-theme-dim-blue-300 dark:text-theme-dark-blue-300"
                        >
                            @lang('brands.arkvault')
                        </x-ark-external-link>
                    </div>
                </div>
            </div>
        </div>
    </x-general.dropdown>
</div>
