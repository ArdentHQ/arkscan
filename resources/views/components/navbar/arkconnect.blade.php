<div
    class="flex flex-col py-3 px-6 md:py-0 md:px-0 dark:border-transparent dark:text-theme-dark-200 dark:bg-theme-dark-900"
    :class="{
        'border-t bg-theme-secondary-200 border-theme-secondary-300 md:border-0 md:bg-transparent': isConnected,
    }"
>
    <div x-show="hasExtension && isSupported">
        <button
            x-show="! isConnected"
            class="py-1.5 px-4 w-full whitespace-nowrap button-secondary"
            @click="connect"
            :disabled="! hasExtension"
            disabled
        >
            @lang('general.navbar.connect_wallet')
        </button>
    </div>

    <x-arkconnect.modal.install-wallet />
    <x-arkconnect.modal.unsupported-browser />

    <div
        x-show="isConnected"
        class="flex justify-between items-center space-x-2 w-full min-w-0 md:hidden"
        x-cloak
    >
        <div class="flex items-center space-x-1 min-w-0 text-sm font-semibold">
            <span class="whitespace-nowrap">
                @lang('general.navbar.arkconnect.my_address'):
            </span>

            <a
                x-bind:href="addressUrl"
                class="flex-1 min-w-0 link"
                x-show="isOnSameNetwork"
            >
                <x-truncate-dynamic />
            </a>

            <a
                data-tippy-content="@lang('general.arkconnect.wrong_network.'.Network::alias())"
                class="flex-1 min-w-0 text-theme-secondary-500 dark:text-theme-dark-500"
                x-show="! isOnSameNetwork"
            >
                <x-truncate-dynamic />
            </a>
        </div>

        <div class="flex space-x-4 md:hidden dark:text-theme-dark-300">
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
                        x-text="truncateMiddle(address)"
                        class="text-sm font-semibold md:leading-3.75"
                    ></div>

                    <div class="dark:text-theme-dark-300">
                        <x-ark-icon
                            name="ellipsis-vertical"
                            size="sm"
                        />
                    </div>
                </x-slot>

                <x-general.dropdown.list-item
                    x-show="isOnSameNetwork"
                    x-bind:href="addressUrl"
                >
                    @lang('general.navbar.arkconnect.my_address')
                </x-general.dropdown.list-item>

                <x-general.dropdown.list-item
                    x-show="!isOnSameNetwork"
                    :data-tippy-content="trans('general.arkconnect.wrong_network.'.Network::alias())"
                    disabled
                >
                    @lang('general.navbar.arkconnect.my_address')
                </x-general.dropdown.list-item>

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
