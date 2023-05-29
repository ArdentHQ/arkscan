@props(['transaction'])

<div class="space-y-2">
    <div class="flex items-center space-x-2">
        <div class="dark:text-theme-secondary-500">@lang('general.search.transaction')</div>

        <a href="{{ $transaction->url() }}" class="min-w-0 link">
            <x-truncate-dynamic>
                {{ $transaction->id() }}
            </x-truncate-dynamic>
        </a>
    </div>

    <div class="flex flex-col md:items-center md:flex-row md:space-x-4 space-y-2 md:space-y-0">
        <div class="flex items-center space-x-2 text-xs">
            <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
                @lang('general.search.from')
            </div>

            <x-general.identity
                :model="$transaction->sender()"
                without-reverse
                without-reverse-class="space-x-2"
                class="text-theme-secondary-700 dark:text-theme-secondary-500"
            >
                <x-slot name="icon">
                    <x-general.avatar-small
                        :identifier="$transaction->sender()->address"
                        size="w-5 h-5"
                    />
                </x-slot>
            </x-general.identity>
        </div>

        <div class="flex items-center space-x-2 text-xs">
            <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
                @lang('general.search.to')
            </div>

            <x-general.identity
                :model="$transaction->recipient()"
                without-reverse
                without-reverse-class="space-x-2"
                class="text-theme-secondary-700 dark:text-theme-secondary-500"
            >
                <x-slot name="icon">
                    <x-general.avatar-small
                        :identifier="$transaction->recipient()->address"
                        size="w-5 h-5"
                    />
                </x-slot>
            </x-general.identity>
        </div>

        <div class="flex items-center space-x-2 text-xs text-right md:flex-1 md:space-x-0">
            <div class="text-theme-secondary-500 dark:text-theme-secondary-700 md:hidden">
                @lang('general.search.amount')
            </div>

            <div class="text-theme-secondary-700 dark:text-theme-secondary-500">
                <x-currency :currency="Network::currency()">
                    {{ ExplorerNumberFormatter::number($transaction->amount()) }}
                </x-currency>
            </div>
        </div>
    </div>
</div>
