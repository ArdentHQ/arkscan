@props(['wallet'])

<x-ark-container x-data="{}">
    <div class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:rounded-lg md:border md:border-theme-secondary-300 md:dark:border-theme-secondary-800 font-semibold overflow-hidden">
        <div class="md:px-4 md:py-[14.5px] md:bg-theme-secondary-200 md:dark:bg-black text-sm md:text-lg dark:text-theme-secondary-500 !leading-[17px] md:!leading-[21px]">
            @lang('general.address')
        </div>

        <div class="flex flex-col space-y-4 md:space-y-0 items-center md:flex-row justify-between flex-1 md:px-3 text-theme-secondary-900 dark:text-theme-secondary-200 leading-5 md:leading-[21px]">
            <x-truncate-dynamic>{{ $wallet->address() }}</x-truncate-dynamic>

            <div class="flex space-x-2 w-full md:w-auto">
                <x-ark-clipboard
                    :value="$wallet->address()"
                    class="flex items-center h-auto p-2 w-full"
                    wrapper-class="flex-1"
                />

                @unless($wallet->isCold())
                    <x-page-headers.wallet.actions.public-key :public-key="$wallet->publicKey()">
                        <x-slot
                            name="button"
                            class="button button-secondary button-icon p-2 w-full"
                        >
                            <x-ark-icon name="key" size="sm" />
                        </x-slot>
                    </x-page-headers.wallet.actions.public-key>
                @endunless

                <livewire:wallet-qr-code
                    :address="$wallet->address()"
                    class="flex-1"
                />
            </div>
        </div>
    </div>
</x-ark-container>
