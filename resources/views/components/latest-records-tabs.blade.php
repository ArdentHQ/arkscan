@props(['transactionType', 'selected'])

<div>
    <x-tabs.wrapper
        class="hidden mb-4 md:flex"
        default-selected="{{ $selected }}"
        on-selected="function (value) {
            if (this.selected !== value) {
                this.$wire.set('state.selected', value);
            }
        }"
    >
        <x-tabs.tab name="transactions">
            @lang('pages.home.latest_transactions')
        </x-tabs.tab>

        <x-tabs.tab name="blocks">
            @lang('pages.home.latest_blocks')
        </x-tabs.tab>

        @if($this->state['selected'] === 'transactions')
            <x-slot name="right">
                <x-transaction-table-filter />
            </x-slot>
        @endif
    </x-tabs.wrapper>

    <div class="mb-5 md:hidden">
        <x-ark-dropdown
            wrapper-class="relative p-2 w-full rounded-xl border border-theme-primary-100 dark:border-theme-secondary-800"
            button-class="p-3 w-full font-semibold text-left text-theme-secondary-900 dark:text-theme-secondary-200"
            dropdown-classes="left-0 w-full z-20"
            :init-alpine="false"
            dropdown-property="tabsOpen"
        >
            <x-slot name="button">
                <div class="flex items-center space-x-4">
                    <div wire:ignore>
                        <div x-show="tabsOpen !== true">
                            <x-ark-icon name="menu" size="sm" />
                        </div>

                        <div x-show="tabsOpen === true">
                            <x-ark-icon name="menu-show" size="sm" />
                        </div>
                    </div>

                    <div>@lang('pages.home.latest_' . $selected)</div>
                </div>
            </x-slot>

            <div class="block items-center py-3 mt-1">
                <button
                    wire:key="btn-latest-transactions"
                    type="button"
                    x-on:click="function () {
                        if (this.selected !== 'transactions') {
                            $wire.set('state.selected', 'transactions');
                            this.selected = 'transactions';
                        }
                    }"
                    @class([
                        'dropdown-entry',
                        'dropdown-entry-selected' => $selected === 'transactions',
                    ])
                >
                    @lang('pages.home.latest_transactions')
                </button>

                <button
                    wire:key="btn-latest-blocks"
                    type="button"
                    x-on:click="function () {
                        if (this.selected !== 'blocks') {
                            $wire.set('state.selected', 'blocks');
                            this.selected = 'blocks';
                        }
                    }"
                    @class([
                        'dropdown-entry',
                        'dropdown-entry-selected' => $selected === 'blocks',
                    ])
                >
                    @lang('pages.home.latest_blocks')
                </button>
            </div>
        </x-ark-dropdown>

        @if($selected === 'transactions')
            <div class="mt-3">
                <x-transaction-table-filter />
            </div>
        @endif
    </div>
</div>
