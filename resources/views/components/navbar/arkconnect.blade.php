<div
    x-show="hasExtension() && ! isLoading"
    x-data="Wallet()"
    x-cloak
>
    <button
        x-show="! isConnected"
        class="button-secondary whitespace-nowrap py-1.5 px-4"
        @click="connect"
    >
        @lang('general.navbar.connect_wallet')
    </button>

    <div
        x-show="isConnected"
        x-init="{
            dropdownOpen: false,
        }"
    >
        <x-general.dropdown.dropdown
            placement="bottom"
            :placement-fallbacks="['bottom-end', 'left-start']"
            dropdown-class="w-full table-filter md:w-[158px]"
            dropdown-background="bg-white dark:bg-theme-dark-700"
            dropdown-padding="py-1"
            dropdown-wrapper-class="w-full"
            active-button-class="flex items-center justify-between md:justify-start space-x-2 bg-transparent whitespace-nowrap pl-3 pr-2 h-8 border border-theme-secondary-300 dark:border-theme-dark-700 text-theme-secondary-700 dark:text-theme-dark-200 hover:text-theme-secondary-900 rounded hover:bg-theme-secondary-200 dark:hover:bg-theme-dark-700 transition-default"
        >
            <x-slot name="button">
                <div
                    x-text="truncateMiddle(await address())"
                    class="text-sm font-semibold leading-3.75"
                ></div>

                <div class="dark:text-theme-dark-300">
                    <x-ark-icon
                        name="ellipsis-vertical"
                        size="sm"
                    />
                </div>
            </x-slot>

            <x-general.dropdown.list-item x-on:click="copy">
                @lang('general.navbar.arkconnect.copy_address')
            </x-general.dropdown.list-item>

            <x-general.dropdown.list-item x-on:click="disconnect">
                @lang('general.navbar.arkconnect.disconnect')
            </x-general.dropdown.list-item>
        </x-general.dropdown.dropdown>
    </div>
</div>
