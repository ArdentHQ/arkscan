<div
    x-data="{ showOptions: false }"
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
                class="font-normal text-theme-secondary-700 dark:text-theme-dark-500"
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
                    :placeholder="trans('pages.wallet.qrcode.memo_optional')"
                    :errors="$errors"
                    hide-label
                />
            </div>

            <div class="flex flex-col items-center">
                <div class="inline-block p-2 rounded-lg border sm:block border-theme-secondary-300 dark:border-theme-dark-300 bg-white">
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
                    class="mt-4 mb-1 font-normal text-theme-secondary-700 dark:text-theme-dark-500"
                >
                    @lang('pages.wallet.qrcode.automatic_notice')
                </div>

                <div class="flex items-center mt-2 space-x-3">
                    <div class="flex-1 border-t h-1px border-theme-secondary-300 dark:border-theme-dark-800"></div>
                    <div class="font-semibold text-theme-secondary-700">@lang('general.or')</div>
                    <div class="flex-1 border-t h-1px border-theme-secondary-300 dark:border-theme-dark-800"></div>
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
    </x-general.dropdown>
</div>
