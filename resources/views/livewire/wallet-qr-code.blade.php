<div
    x-data="{ showOptions: false }"
    @class($class)
>
    <x-general.dropdown.dropdown
        width="max-w-[288px]"
        active-button-class="button button-secondary button-icon"
        dropdown-padding=""
        dropdown-background="bg-white dark:bg-theme-secondary-900"
        :close-on-click="false"
    >
        <x-slot
            name="button"
            wire:click="toggleQrCode"
            class="p-2 w-full "
        >
            <div>
                <x-ark-icon name="qr-code" size="sm" />
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="flex items-center p-6 justify-between border-b border-theme-secondary-300 dark:border-theme-secondary-800">
                <div class="text-lg">
                    @lang('pages.wallet.qrcode.title')
                </div>

                <div>
                    <button
                        type="button"
                        class="button button-generic hover:text-white p-2 hover:bg-theme-primary-700"
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
                    class="text-theme-secondary-700 font-normal"
                >
                    @lang('pages.wallet.qrcode.description')
                </div>

                <div
                    x-show="showOptions"
                    class="space-y-3 pt-2 pb-4"
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
                    <div class="border border-theme-secondary-300 dark:border-theme-secondary-300 dark:bg-theme-secondary-300 rounded-lg p-2">
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
                        class="mt-4 mb-1 text-theme-secondary-700 font-normal"
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
