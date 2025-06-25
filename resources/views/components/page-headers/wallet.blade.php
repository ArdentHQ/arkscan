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
            <x-page-headers.wallet.actions.public-key :public-key="$wallet->publicKey()" />
        @endunless

        <x-page-headers.wallet.actions.legacy-address :address="$wallet->legacyAddress()" />

        <livewire:wallet-qr-code
            :address="$wallet->address()"
            class="flex-1"
        />
    </x-slot>
</x-page-headers.container>
