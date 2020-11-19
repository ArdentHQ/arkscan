<div class="dark:bg-theme-secondary-900">
    <div class="flex-col pt-16 mb-16 space-y-5 content-container">
        <x-general.search.header-slim :title="trans($title)" />

        <x-general.entity-header :value="$wallet->address()">
            <x-slot name="title">
                @isset($useGenerator)
                    @lang('pages.wallet.address_generator', [$wallet->username()])
                @else
                    @if($wallet->isDelegate())
                        {{ $wallet->username() }}
                    @else
                        @lang('pages.wallet.address')
                    @endif
                @endif

                <div class="ml-2 divide-x divide-gray-400 wallet-icons-row">
                    @if ($wallet->isKnown())
                        <div data-tippy-content="@lang('labels.verified_address')">
                            <x-ark-icon name="app-verified" size="sm" />
                        </div>
                    @endif

                    @if ($wallet->hasMultiSignature())
                        <div data-tippy-content="@lang('labels.multi_signature')">
                            <x-ark-icon name="app.transactions-multi-signature" size="sm" />
                        </div>
                    @endif

                    @if ($wallet->isOwnedByExchange())
                        <div data-tippy-content="@lang('labels.exchange')">
                            <x-ark-icon name="app-exchange" size="sm" />
                        </div>
                    @endif

                    @if ($wallet->hasSecondSignature())
                        <div data-tippy-content="@lang('labels.second_signature')">
                            <x-ark-icon name="app.transactions-second-signature" size="sm" />
                        </div>
                    @endif
                </div>
            </x-slot>

            <x-slot name="logo">
                @if($wallet->isDelegate())
                    @if ($wallet->isResigned())
                        <x-page-headers.avatar-with-icon :model="$wallet" icon="app.transactions-delegate-resignation" />
                    @else
                        <x-page-headers.avatar-with-icon :model="$wallet" icon="app-delegate" />
                    @endif
                @else
                    <x-page-headers.avatar :model="$wallet" />
                @endif
            </x-slot>

            @if($wallet->isDelegate())
                <x-slot name="extraLogo">
                    <div class="lg:hidden circled-icon text-theme-secondary-400 border-theme-danger-400">
                        <x-ark-icon name="app-delegate" />
                    </div>
                </x-slot>
            @endif

            <x-slot name="extension">
                {{ $slot }}

                <div class="flex items-center mt-6 space-x-2 text-theme-secondary-200 lg:mt-0">
                    @unless($wallet->isCold())
                        <x-page-headers.wallet.actions.public-key :public-key="$wallet->publicKey()" />
                    @endunless

                    <x-page-headers.wallet.actions.qr-code :wallet="$wallet" />
                </div>
            </x-slot>

            @isset($extension)
                <x-slot name="bottom">
                    {{ $extension }}
                </x-slot>
            @endisset
        </x-general.entity-header>
    </div>
</div>
