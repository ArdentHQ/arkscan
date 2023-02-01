@props([
    'title',
    'wallet',
])

<x-ark-container container-class="flex flex-col space-y-6">
    <h1>
        @lang($title)
    </h1>

    <x-general.entity-header 
        :value="$wallet->address()" 
        padding="lg:pl-8 lg:pr-7 px-7 lg:py-5 py-6"
    >
        <x-slot name="title">
            @lang('pages.wallet.migration_wallet')

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
            <x-ark-icon 
                name="app-transactions.migration" 
                size="w-11 h-11" 
                class="migration-icon-dark" 
            />
        </x-slot>

        @unless($wallet->isCold())
            <x-slot name="valueExtension">
                <x-page-headers.wallet.actions.public-key :public-key="$wallet->publicKey()" />
            </x-slot>
        @endunless

        <x-slot name="extension">
            {{ $slot }}

            <div class="flex mt-2 sm:hidden">
                <x-page-headers.wallet.actions.qr-code :wallet="$wallet" />
            </div>
        </x-slot>
    </x-general.entity-header>
</x-ark-container>
