<div x-data="{ tab: 'transactions' }">
    <x-tabs.inline-wrapper
        class="hidden mb-4 md:inline-flex"
        default-selected="transactions"
        on-selected="function (value) {
            this.$wire.set('state.view', value);
        }"
    >
        <x-tabs.inline-tab
            name="transactions"
            first
        >
            <span>@lang('pages.wallet.transactions')</span>
        </x-tabs.inline-tab>

        @if($wallet->isDelegate())
            <x-tabs.inline-tab name="blocks">
                <span>@lang('pages.wallet.delegate.validated_blocks')</span>
            </x-tabs.inline-tab>

            <x-tabs.inline-tab name="voters">
                <span>@lang('pages.wallet.delegate.voters')</span>
            </x-tabs.inline-tab>
        @endif
    </x-tabs.wrapper>

    <div
        wire:key="{{ Illuminate\Support\Str::random(20) }}"
        class="mb-5 md:hidden md:space-x-3"
    >
        <x-ark-dropdown
            wrapper-class="relative w-full rounded border md:w-1/2 border-theme-secondary-300 dark:border-theme-secondary-800"
            button-class="justify-between py-3 px-4 w-full font-semibold text-left text-theme-secondary-900 dark:text-theme-secondary-200"
            dropdown-classes="left-0 w-full z-20"
            :init-alpine="false"
        >
            <x-slot name="button">
                <div
                    class="flex items-center"
                    wire:ignore
                >
                    <div x-show="tab === 'transactions'">@lang('pages.wallet.transactions')</div>

                    @if($wallet->isDelegate())
                        <div x-show="tab === 'blocks'">@lang('pages.wallet.delegate.validated_blocks')</div>
                        <div x-show="tab === 'voters'">@lang('pages.wallet.delegate.voters')</div>
                    @endif
                </div>

                <span
                    class="transition-default"
                    :class="{ 'rotate-180': dropdownOpen }"
                >
                    <x-ark-icon
                        name="arrows.chevron-down-small"
                        size="w-3 h-3"
                        class="text-theme-secondary-700 dark:text-theme-secondary-200"
                    />
                </span>
            </x-slot>

            <div class="block justify-center items-center py-3 mt-1">
                <a
                    wire:click="$set('state.view', 'transactions');"
                    @click="view = 'transactions'; tab = 'transactions';"
                    class="dropdown-entry @if($state['view'] === 'transactions') dropdown-entry-selected @endif"
                >
                    @lang('pages.wallet.transactions')
                </a>

                @if($wallet->isDelegate())
                    <a
                        wire:click="$set('state.view', 'blocks');"
                        @click="view = 'blocks'; tab = 'blocks';"
                        class="dropdown-entry @if($state['view'] === 'blocks') dropdown-entry-selected @endif"
                    >
                        <span>@lang('pages.wallet.delegate.validated_blocks')</span>
                    </a>

                    <a
                        wire:click="$set('state.view', 'voters');"
                        @click="view = 'voters'; tab = 'voters';"
                        class="dropdown-entry @if($state['view'] === 'voters') dropdown-entry-selected @endif"
                    >
                        <span>@lang('pages.wallet.delegate.voters')</span>
                    </a>
                @endif
            </div>
        </x-ark-dropdown>
    </div>

    <div id="transaction-list" class="w-full">
        <x-skeletons.transactions>
            @if ($transactions->isEmpty())
                <x-general.no-results :text="trans('pages.home.no_transaction_results', [trans('forms.search.transaction_types.all')])" />
            @else
                <x-tables.toolbars.transactions :transactions="$transactions" />

                <x-tables.desktop.wallet-transactions
                    :transactions="$transactions"
                    :wallet="$wallet"
                    :state="$this->state()"
                />

                <x-tables.mobile.wallet-transactions
                    :transactions="$transactions"
                    :wallet="$wallet"
                    :state="$this->state()"
                />

                <x-general.pagination :results="$transactions" class="mt-8" />
            @endif

            <x-script.onload-scroll-to-query selector="#transaction-list" />
        </x-skeletons.transactions>
    </div>
</div>
