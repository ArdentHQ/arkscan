<div class="dark:bg-theme-secondary-900">
    <x-ark-container container-class="flex flex-col space-y-5">
        <h1 class="header-2">
            @lang($title)
        </h1>

        <x-general.entity-header :value="$wallet->address()" padding="lg:pl-8 lg:pr-7 px-7 lg:py-5 py-6">
            <x-slot name="title">
                @isset($useGenerator)
                    <span class="hidden xl:inline">@lang('pages.wallet.generated_by')&nbsp;</span>
                    <span>{{ $wallet->username() }}</span>
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

                <div class="flex flex-col-reverse items-center p-1 mt-6 space-y-2 sm:space-x-2 sm:space-y-0 sm:flex-row text-theme-secondary-200 lg:mt-0 lg:ml-4">
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
    </x-ark-container>
</div>
