@php
    $showReceiveLabel = !$wallet->isDelegate() || $wallet->isResigned();
@endphp

<div @class([
    'w-full' => $showReceiveLabel
])>
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

                    <a
                        href="{{ $this->walletUri }}"
                        class="mt-2 w-full button-secondary"
                        target="_blank"
                    >
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
        @class([
            "flex flex-1 justify-center items-center rounded cursor-pointer w-full bg-theme-secondary-800 transition-default h-11 hover:bg-theme-primary-700 text-white flex-shrink-0",
            "sm:w-14 w-full" => $showReceiveLabel,
            "w-14 " => !$showReceiveLabel,
        ])
    >
        <x-ark-icon name="qr-code" size="sm" />

        @if($showReceiveLabel)
            <span class="ml-2 font-semibold sm:hidden">@lang('actions.receive')</span>
        @endif
    </button>
</div>
