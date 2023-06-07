@props(['wallet'])

<div
    x-data="{}"
    class="pt-8 pb-6 px-6 md:mx-auto md:max-w-7xl md:px-10 flex flex-col"
>
    <div class="flex overflow-hidden flex-col space-y-4 font-semibold sm:flex-row sm:items-end sm:justify-between md:items-center sm:space-y-0 md:rounded-lg md:border md:border-theme-secondary-300 md:dark:border-theme-secondary-800">
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:items-center md:space-x-3">
            <div class="md:px-4 md:py-[14.5px] md:bg-theme-secondary-200 md:dark:bg-black text-sm md:text-lg dark:text-theme-secondary-500 !leading-[17px] md:!leading-[21px]">
                @lang('general.address')
            </div>

            <div class="text-theme-secondary-900 dark:text-theme-secondary-200 leading-5 md:leading-[21px]">
                <x-truncate-dynamic>{{ $wallet->address() }}</x-truncate-dynamic>
            </div>
        </div>

        <div class="flex space-x-2 w-full sm:w-auto md:px-3">
            <x-ark-clipboard
                :value="$wallet->address()"
                class="flex items-center p-2 w-full h-auto"
                wrapper-class="flex-1"
            />

            @unless($wallet->isCold())
                <x-page-headers.wallet.actions.public-key :public-key="$wallet->publicKey()">
                    <x-slot
                        name="button"
                        class="p-2 w-full button button-secondary button-icon"
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
