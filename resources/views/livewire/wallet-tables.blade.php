<div
    x-data="{ tab: @entangle('view') }"
    wire:init="triggerViewIsReady"
>
    <x-tabs.inline-wrapper
        x-data="{
            init: function () {
                this.$watch('tab', () => {
                    this.selected = this.tab;
                });
            },
        }"
        class="hidden mb-3 md:inline-flex"
        :default-selected="$this->view"
        on-selected="function (value) {
            this.$wire.set('view', value);

            this.tab = value;
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
                    wire:click="$set('view', 'transactions');"
                    @click="view = 'transactions'; tab = 'transactions';"
                    @class([
                        'dropdown-entry',
                        'dropdown-entry-selected' => $this->view === 'transactions',
                    ])
                >
                    @lang('pages.wallet.transactions')
                </a>

                @if($wallet->isDelegate())
                    <a
                        wire:click="$set('view', 'blocks');"
                        @click="view = 'blocks'; tab = 'blocks';"
                        @class([
                            'dropdown-entry',
                            'dropdown-entry-selected' => $this->view === 'blocks',
                        ])
                    >
                        <span>@lang('pages.wallet.delegate.validated_blocks')</span>
                    </a>

                    <a
                        wire:click="$set('view', 'voters');"
                        @click="view = 'voters'; tab = 'voters';"
                        @class([
                            'dropdown-entry',
                            'dropdown-entry-selected' => $this->view === 'voters',
                        ])
                    >
                        <span>@lang('pages.wallet.delegate.voters')</span>
                    </a>
                @endif
            </div>
        </x-ark-dropdown>
    </div>

    <div id="wallet-table-list">
        <x-wallet.tables.transactions :wallet="$wallet" />

        @if($wallet->isDelegate())
            <x-wallet.tables.voters :wallet="$wallet" x-cloak />

            <x-wallet.tables.blocks :wallet="$wallet" x-cloak />
        @endif

        <x-script.onload-scroll-to-query selector="#wallet-table-list" />
    </div>
</div>
