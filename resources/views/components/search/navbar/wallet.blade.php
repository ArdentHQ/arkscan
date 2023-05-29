@props(['wallet'])

<div class="space-y-2">
    <div class="flex items-center space-x-2 overflow-auto">
        <div class="dark:text-theme-secondary-500">@lang('general.search.address')</div>

        <x-general.identity
            :model="$wallet"
            without-reverse
            without-reverse-class="space-x-2"
            class="text-theme-secondary-700 dark:text-theme-secondary-500"
        >
            <x-slot name="icon">
                <x-general.avatar-small
                    :identifier="$wallet->address()"
                    size="w-5 h-5"
                />
            </x-slot>
        </x-general.identity>
    </div>

    <div class="flex items-center space-x-1">
        <div class="text-xs text-theme-secondary-500 dark:text-theme-secondary-700">
            @lang('general.search.balance')
        </div>

        <div class="text-sm dark:text-theme-secondary-500">
            <x-currency :currency="Network::currency()">
                {{ ExplorerNumberFormatter::number($wallet->balance()) }}
            </x-currency>
        </div>
    </div>
</div>
