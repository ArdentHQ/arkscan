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
            name="delegates"
            first
        >
            <span>@lang('pages.delegates.tabs.delegates')</span>
        </x-tabs.inline-tab>

        <x-tabs.inline-tab name="missed-blocks">
            <span>@lang('pages.delegates.tabs.missed-blocks')</span>
        </x-tabs.inline-tab>

        <x-tabs.inline-tab name="recent-votes">
            <span>@lang('pages.delegates.tabs.recent-votes')</span>
        </x-tabs.inline-tab>
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
                    <div x-show="tab === 'delegates'">
                        @lang('pages.delegates.tabs.delegates')
                    </div>
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
                    wire:click="$set('view', 'delegates');"
                    @click="view = 'delegates'; tab = 'delegates';"
                    @class([
                        'dropdown-entry',
                        'dropdown-entry-selected' => $this->view === 'delegates',
                    ])
                >
                    @lang('pages.delegates.tabs.delegates')
                </a>
            </div>
        </x-ark-dropdown>
    </div>

    <div id="delegate-table-list">
        <x-delegates.tables.delegates />

        <x-script.onload-scroll-to-query selector="#delegate-table-list" />
    </div>
</div>
