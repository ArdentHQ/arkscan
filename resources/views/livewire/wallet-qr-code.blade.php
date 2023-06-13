<div
    x-data="{ showOptions: false }"
    @class($class)
>
    <x-general.dropdown.dropdown
        width="max-w-[320px] px-4"
        active-button-class="button button-secondary button-icon"
        dropdown-padding=""
        dropdown-wrapper-class="w-full"
        dropdown-background="bg-white dark:bg-theme-secondary-900"
        :close-on-click="false"
    >
        <x-slot
            name="button"
            wire:click="toggleQrCode"
            class="p-2 w-full"
        >
            <div>
                <x-ark-icon name="qr-code" size="sm" />
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="flex justify-between items-center p-6 border-b border-theme-secondary-300 dark:border-theme-secondary-800">
                <div class="text-lg text-theme-secondary-900 dark:text-theme-secondary-200">
                    @lang('pages.wallet.qrcode.title')
                </div>

                <div>
                    <button
                        type="button"
                        class="p-2 hover:text-white button button-generic dark:hover:text-white dark:text-theme-secondary-600 hover:bg-theme-primary-700"
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
                    class="font-normal text-theme-secondary-700"
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
                        :errors="$errors"
                        :placeholder="trans('pages.wallet.qrcode.currency_amount', ['currency' => Network::currency()])"
                        hide-label
                        autofocus
                    />

                    <x-ark-textarea
                        id="smartbridge"
                        name="smartbridge"
                        maxlength="255"
                        rows="4"
                        class="font-normal"
                        :placeholder="trans('pages.wallet.qrcode.memo')"
                        :errors="$errors"
                        hide-label
                    />
                </div>

                <div>
                    <div class="p-2 rounded-lg border border-theme-secondary-300 dark:border-theme-secondary-300 dark:bg-theme-secondary-300">
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
                        class="mt-4 mb-1 font-normal text-theme-secondary-700"
                    >
                        @lang('pages.wallet.qrcode.automatic_notice')
                    </div>

                    <div class="flex items-center mt-2 space-x-3">
                        <div class="flex-1 border-t h-1px border-theme-secondary-300 dark:border-theme-secondary-800"></div>
                        <div class="font-semibold text-theme-secondary-700">@lang('general.or')</div>
                        <div class="flex-1 border-t h-1px border-theme-secondary-300 dark:border-theme-secondary-800"></div>
                    </div>

                    <a
                        href="{{ $this->walletUri }}"
                        class="mt-2 w-full button-primary"
                        target="_blank"
                    >
                        @lang('pages.wallet.qrcode.send_from_wallet')
                    </a>
                </div>
            </div>
        </x-slot>
    </x-general.dropdown>
</div>
