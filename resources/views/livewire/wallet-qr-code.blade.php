<div class="flex-1 w-full sm:w-auto">
    @if($this->modalShown)
    <x-ark-modal width-class="max-w-md" wire-close="closeModal" title-class="header-2" x-data="{ options: false }">
        @slot('title')
            @lang('pages.wallet.qrcode.title')
        @endslot

        @slot('description')
            <div class="space-y-8">
                <div class="mt-4 text-theme-secondary-700">@lang('pages.wallet.qrcode.description')</div>

                <button x-show="! options" class="w-full button-secondary" @click="options = true">
                    @lang('pages.wallet.qrcode.specify_amount')
                </button>

                <div class="pb-6 border-b border-dashed border-theme-secondary-300 dark:border-theme-secondary-800">
                    <x-wallet.qr-address :model="$wallet" />
                </div>

                <div x-show="options" class="space-y-5">
                    <x-ark-input
                        :label="trans('pages.wallet.amount')"
                        type="number"
                        id="amount"
                        name="amount"
                        max="17"
                        :errors="$errors"
                        autofocus
                    />

                    <x-ark-input
                        type="text"
                        id="smartbridge"
                        name="smartbridge"
                        max="255"
                        :errors="$errors"
                    >
                        <x-slot name="label">
                            <div class="flex space-x-1">
                                <span>@lang('pages.wallet.smartbridge')</span>

                                <span class="text-theme-secondary-500">@lang('general.optional')</span>
                            </div>
                        </x-slot>
                    </x-ark-input>
                </div>

                <div>
                    <div class="modal-qr-code">
                        {!! $this->code !!}
                    </div>

                    <div x-show="options" class="mt-4 text-theme-secondary-700">
                        <div class="text-center">
                            @lang('pages.wallet.qrcode.automatic_notice')
                        </div>
                    </div>

                    <div class="flex items-center mt-6 space-x-3">
                        <div class="flex-1 border-t h-1px border-theme-secondary-300 dark:border-theme-secondary-800"></div>
                        <div class="text-sm font-semibold text-theme-secondary-700">@lang('general.or')</div>
                        <div class="flex-1 border-t h-1px border-theme-secondary-300 dark:border-theme-secondary-800"></div>
                    </div>

                    <a href="{{ $this->walletUri }}" class="mt-2 w-full button-secondary">
                        @lang('pages.wallet.qrcode.send_from_wallet')
                    </a>
                </div>
            </div>
        @endslot
    </x-ark-modal>
    @endif

    <button
        wire:click="toggleQrCode"
        type="button"
        class="flex flex-1 justify-center items-center w-full h-11 rounded cursor-pointer lg:flex-none lg:px-3 lg:w-16 bg-theme-primary-600 transition-default hover:bg-theme-primary-700"
    >
        <x-ark-icon name="app-qr-code" size="md" />
    </button>
</div>
