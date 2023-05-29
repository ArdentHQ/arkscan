@props(['transaction'])

<div class="space-y-2 pt-4 ">
    <div class="flex items-center space-x-2">
        <div>@lang('general.search.transaction')</div>

        <a href="{{ $transaction->url() }}" class="min-w-0 link">
            <x-truncate-dynamic>
                {{ $transaction->id() }}
            </x-truncate-dynamic>
        </a>
    </div>

    <div class="flex flex-col md:items-center md:flex-row md:space-x-4 space-y-2 md:space-y-0">
        <div class="flex items-center space-x-2 text-xs">
            <div class="text-theme-secondary-500">
                @lang('general.search.from')
            </div>

            <x-general.identity
                :model="$transaction->sender()"
                without-reverse
                without-reverse-class="space-x-2"
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
            <div class="text-theme-secondary-500">
                @lang('general.search.to')
            </div>

            <x-general.identity
                :model="$transaction->recipient()"
                without-reverse
                without-reverse-class="space-x-2"
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
            <div class="text-theme-secondary-500 md:hidden">
                @lang('general.search.amount')
            </div>

            <div>
                <x-currency :currency="Network::currency()">
                    {{ ExplorerNumberFormatter::number($transaction->amount()) }}
                </x-currency>
            </div>
        </div>
    </div>
</div>
