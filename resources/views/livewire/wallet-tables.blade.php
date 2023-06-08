<div class="">
    <x-tabs.inline-wrapper
        class="hidden mb-4 lg:inline-flex"
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
        class="mb-5 md:flex md:space-x-3 lg:hidden"
    >
        <x-ark-dropdown
            wrapper-class="relative p-2 w-full rounded-xl border md:w-1/2 border-theme-primary-100 dark:border-theme-secondary-800"
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

                    <div x-show="view === 'transactions'">@lang('pages.wallet.transactions')</div>

                    @if($wallet->isDelegate())
                        <div x-show="view === 'blocks'">@lang('pages.wallet.delegate.validated_blocks')</div>
                        <div x-show="view === 'voters'">@lang('pages.wallet.delegate.voters')</div>
                    @endif
                </div>
            </x-slot>

            <div class="block justify-center items-center py-3 mt-1">
                <a
                    wire:click="$set('state.view', 'transactions');"
                    @click="view = 'transactions'"
                    class="dropdown-entry @if($state['view'] === 'transactions') dropdown-entry-selected @endif"
                >
                    @lang('pages.wallet.transactions')
                </a>

                @if($wallet->isDelegate())
                    <a
                        wire:click="$set('state.view', 'blocks');"
                        @click="view = 'blocks'"
                        class="dropdown-entry @if($state['view'] === 'blocks') dropdown-entry-selected @endif"
                    >
                        <span>@lang('pages.wallet.delegate.validated_blocks')</span>
                    </a>

                    <a
                        wire:click="$set('state.view', 'voters');"
                        @click="view = 'voters'"
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
            @if ($transactions->isEmpty() && $state['type'] !== 'all')
                <x-general.no-results :text="trans('pages.home.no_transaction_results', [trans('forms.search.transaction_types.'.$state['type'])])" />
            @else
                <x-tables.desktop.transactions
                    :transactions="$transactions"
                    :wallet="$wallet"
                    use-confirmations
                    use-direction
                    exclude-itself
                    {{-- :exclude-itself="$state['direction'] === 'all'" --}}
                    {{-- :is-sent="$state['direction'] === 'sent'" --}}
                    {{-- :is-received="$state['direction'] === 'received'" --}}
                    :state="$state"
                />

                <x-tables.mobile.transactions
                    :transactions="$transactions"
                    :wallet="$wallet"
                    use-confirmations
                    use-direction
                    exclude-itself
                    {{-- :exclude-itself="$state['direction'] === 'all'" --}}
                    {{-- :is-sent="$state['direction'] === 'sent'" --}}
                    {{-- :is-received="$state['direction'] === 'received'" --}}
                    :state="$state"
                />

                <x-general.pagination :results="$transactions" class="mt-8" />
            @endif

            <x-script.onload-scroll-to-query selector="#transaction-list" />
        </x-skeletons.transactions>
    </div>
</div>
