<div
    x-data="Wallet()"
    class="flex flex-col py-3 px-6 md:py-0 md:px-0"
    :class="{
        'border-t bg-theme-secondary-200 border-theme-secondary-300 md:border-0 md:bg-transparent': isConnected,
    }"
>
    <button
        x-show="! isConnected"
        class="py-1.5 px-4 whitespace-nowrap button-secondary"
        @click="connect"
        :disabled="! hasExtension() || isLoading"
    >
        @lang('general.navbar.connect_wallet')
    </button>

    <div
        x-show="isConnected"
        class="flex justify-between items-center space-x-2 w-full min-w-0 md:hidden"
        x-cloak
    >
        <div class="flex items-center space-x-1 min-w-0 text-sm font-semibold">
            <span class="hidden whitespace-nowrap xs:block">
                @lang('general.navbar.arkconnect.my_address')
            </span>

            <div class="flex-1 min-w-0">
                <x-truncate-dynamic />
            </div>
        </div>

        <div class="flex space-x-4 md:hidden">
            <a
                x-on:click="copy"
                data-tippy-content="@lang('tooltips.copied')"
            >
                <x-ark-icon name="copy" />
            </a>

            <a x-on:click="disconnect">
                <x-ark-icon name="arrows.arrow-right-bracket" />
            </a>
        </div>
    </div>

    <div x-cloak>
        <div
            x-show="isConnected"
            x-init="{
                dropdownOpen: false,
            }"
            class="hidden md:block"
        >
            <x-general.dropdown.dropdown
                placement="bottom"
                :placement-fallbacks="['bottom-end', 'left-start']"
                dropdown-class="w-full table-filter md:w-[158px]"
                dropdown-background="bg-white dark:bg-theme-dark-700"
                dropdown-padding="py-1"
                dropdown-wrapper-class="w-full"
                active-button-class="flex justify-between items-center whitespace-nowrap bg-transparent rounded md:justify-start md:pr-2 md:pl-3 md:space-x-2 md:h-8 md:border border-theme-secondary-300 text-theme-secondary-700 transition-default dark:border-theme-dark-700 dark:text-theme-dark-200 dark:hover:bg-theme-dark-700 hover:text-theme-secondary-900 hover:bg-theme-secondary-200"
            >
                <x-slot name="button">
                    <div
                        x-text="truncateMiddle(await address())"
                        class="text-sm font-semibold md:leading-3.75"
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
</div>
