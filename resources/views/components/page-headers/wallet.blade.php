@props(['wallet'])

<x-page-headers.container :label="trans('general.address')">
    <x-truncate-dynamic>{{ $wallet->address() }}</x-truncate-dynamic>

    <x-slot name="extra">
        <x-ark-clipboard
            :value="$wallet->address()"
            class="flex items-center p-2 w-full h-auto focus-visible:ring-inset group"
            wrapper-class="flex-1"
            :tooltip-content="trans('pages.wallet.address_copied')"
            with-checkmarks
            checkmarks-class="group-hover:text-white text-theme-primary-900 dark:text-theme-dark-200"
        />

        @unless($wallet->isCold())
            <x-page-headers.wallet.actions.public-key :public-key="$wallet->publicKey()">
                <x-slot
                    name="button"
                    class="p-2 w-full focus-visible:ring-inset button button-secondary button-icon"
                >
                    <x-ark-icon name="key" size="sm" />
                </x-slot>
            </x-page-headers.wallet.actions.public-key>
        @endunless

        <x-wallet.qr-code-modal :address="$wallet->address()" />
    </x-slot>
</x-page-headers.container>
