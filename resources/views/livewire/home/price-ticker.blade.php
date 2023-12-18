<div wire:poll.30s>
    <x-home.stat
        :title="trans('pages.home.statistics.currency_price', ['currency' => Network::currency()])"
        class="md:hidden"
    >
        {{ $price }}

        @if (ExplorerNumberFormatter::hasSymbol(Settings::currency()))
            {{ Settings::currency() }}
        @endif
    </x-home.stat>

    <p class="hidden items-center space-x-2 sm:space-x-3 md:inline-flex">
        <span class="text-sm font-semibold sm:text-3xl md:text-2xl lg:text-xl xl:text-2xl text-theme-secondary-900 dark:text-theme-dark-50">
            <span>
                {{ $price }}

                @if (ExplorerNumberFormatter::hasSymbol(Settings::currency()))
                    {{ Settings::currency() }}
                @endif
            </span>
        </span>
    </p>
</div>
