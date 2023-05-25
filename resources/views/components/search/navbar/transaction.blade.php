@props(['transaction'])

<div class="space-y-2">
    <div class="flex items-center space-x-2">
        <div>@lang('general.search.transaction')</div>

        <a href="{{ $transaction->url() }}" class="link min-w-0">
            <x-truncate-dynamic>
                {{ $transaction->id() }}
            </x-truncate-dynamic>
        </a>
    </div>

    <div class="flex flex-col md-lg:items-center md-lg:flex-row md-lg:space-x-4">
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

        <div class="text-xs md-lg:flex-1 text-right">
            <x-currency :currency="Network::currency()">
                {{ ExplorerNumberFormatter::number($transaction->amount()) }}
            </x-currency>
        </div>
    </div>
</div>
