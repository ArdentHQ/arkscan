@props(['wallet'])

<div class="space-y-2">
    <div class="flex items-center space-x-2">
        <div>@lang('general.search.address')</div>

        <x-general.identity
            :model="$wallet"
            without-reverse
            without-truncate
            without-reverse-class="space-x-2"
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
        <div class="text-xs text-theme-secondary-500">
            @lang('general.search.balance')
        </div>

        <div>
            <x-currency :currency="Network::currency()">
                {{ ExplorerNumberFormatter::number($wallet->balance()) }}
            </x-currency>
        </div>
    </div>
</div>
