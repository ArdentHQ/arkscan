<div class="">
    <x-tabs.wrapper
        class="hidden mb-4 md:flex"
        default-selected="all"
        on-selected="function (value) {
            this.$wire.set('state.direction', value);
        }"
    >
        <x-tabs.tab name="all">
            <span>@lang('pages.wallet.all_transactions')</span>
        </x-tabs.tab>

        <x-tabs.tab name="received">
            <span>@lang('pages.wallet.received_transactions')</span>

            <span class="info-badge">{{ $countReceived }}</span>
        </x-tabs.tab>

        @unless($state['isCold'])
            <x-tabs.tab name="sent">
                <span>@lang('pages.wallet.sent_transactions', [$countSent])</span>

                <span class="info-badge">{{ $countSent }}</span>
            </x-tabs.tab>
        @endunless

        <x-slot name="right">
            <x-transaction-table-filter />
        </x-slot>
    </x-tabs.wrapper>

    <div class="mb-5 md:hidden">
        <x-ark-dropdown
            wrapper-class="relative p-2 w-full rounded-xl border border-theme-primary-100 dark:border-theme-secondary-800"
            button-class="p-3 w-full font-semibold text-left text-theme-secondary-900 dark:text-theme-secondary-200"
            dropdown-classes="left-0 w-full z-20"
            :init-alpine="false"
        >
            <x-slot name="button">
                <div class="flex items-center space-x-4" wire:ignore>
                    <div>
                        <div x-show="dropdownOpen !== true">
                            <x-ark-icon name="menu" size="sm" />
                        </div>

                        <div x-show="dropdownOpen === true">
                            <x-ark-icon name="menu-show" size="sm" />
                        </div>
                    </div>

                    <div x-show="direction === 'all'">@lang('pages.wallet.all_transactions')</div>
                    <div x-show="direction === 'received'">@lang('pages.wallet.received_transactions')</div>
                    @unless($state['isCold'])
                        <div x-show="direction === 'sent'">@lang('pages.wallet.sent_transactions')</div>
                    @endunless
                </div>
            </x-slot>

            <div class="block justify-center items-center py-3 mt-1">
                <a
                    wire:click="$set('state.direction', 'all');"
                    @click="direction = 'all'"
                    class="dropdown-entry @if($state['direction'] === 'all') dropdown-entry-selected @endif"
                >
                    @lang('pages.wallet.all_transactions')
                </a>

                <a
                    wire:click="$set('state.direction', 'received');"
                    @click="direction = 'received'"
                    class="dropdown-entry @if($state['direction'] === 'received') dropdown-entry-selected @endif"
                >
                    <span>@lang('pages.wallet.received_transactions')</span>

                    <span class="info-badge">{{ $countReceived }}</span>
                </a>

                @unless($state['isCold'])
                    <a
                        wire:click="$set('state.direction', 'sent');"
                        @click="direction = 'sent'"
                        class="dropdown-entry @if($state['direction'] === 'sent') dropdown-entry-selected @endif"
                    >
                        <span>@lang('pages.wallet.sent_transactions')</span>

                        <span class="info-badge">{{ $countSent }}</span>
                    </a>
                @endunless
            </div>
        </x-ark-dropdown>

        <div class="mt-3">
            <x-transaction-table-filter />
        </div>
    </div>

    <div id="transaction-list" class="w-full">
        <x-skeletons.transactions>
            @if ($transactions->isEmpty() && $state['type'] !== 'all')
                <x-general.no-results :text="trans('pages.home.no_transaction_results', [trans('forms.search.transaction_types.'.$state['type'])])" />
            @else
                <x-tables.desktop.transactions
                    :transactions="$transactions"
                    :wallet="$wallet"
                    use-confirmations
                    use-direction
                    :exclude-itself="$state['direction'] === 'all'"
                    :is-sent="$state['direction'] === 'sent'"
                    :is-received="$state['direction'] === 'received'"
                    :state="$state"
                />

                <x-tables.mobile.transactions
                    :transactions="$transactions"
                    :wallet="$wallet"
                    use-confirmations
                    use-direction
                    :exclude-itself="$state['direction'] === 'all'"
                    :is-sent="$state['direction'] === 'sent'"
                    :is-received="$state['direction'] === 'received'"
                    :state="$state"
                />

                <x-general.pagination :results="$transactions" class="mt-8" />
            @endif

            <x-script.onload-scroll-to-query selector="#transaction-list" />
        </x-skeletons.transactions>
    </div>
</div>
