<div>
    <div class="hidden tabs md:flex">
        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': direction === 'all' }"
            wire:click="$set('state.direction', 'all');"
            @click="direction = 'all'"
        >
            @lang('pages.wallet.all_transactions')
        </div>

        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': direction === 'received' }"
            wire:click="$set('state.direction', 'received');"
            @click="direction = 'received'"
        >
            <span>@lang('pages.wallet.received_transactions')</span>

            <span class="info-badge">{{ $countReceived }}</span>
        </div>

        @unless($state['isCold'])
            <div
                class="tab-item transition-default"
                :class="{ 'tab-item-current': direction === 'sent' }"
                wire:click="$set('state.direction', 'sent');"
                @click="direction = 'sent'"
            >
                <span>@lang('pages.wallet.sent_transactions', [$countSent])</span>

                <span class="info-badge">{{ $countSent }}</span>
            </div>
        @endunless
    </div>

    <div class="md:hidden">
        <x-ark-dropdown
            wrapper-class="relative w-full p-2 mb-8 border rounded-lg border-theme-secondary-300 dark:border-theme-secondary-800"
            button-class="w-full font-semibold text-left text-theme-secondary-900 dark:text-theme-secondary-200"
            dropdown-classes="left-0 w-full z-20"
            :init-alpine="false"
        >
            <x-slot name="button">
                <div class="flex items-center space-x-4">
                    <x-icon name="menu-open" size="md" />

                    <div x-show="direction === 'all'">@lang('pages.wallet.all_transactions')</div>
                    <div x-show="direction === 'received'">@lang('pages.wallet.received_transactions')</div>
                    @unless($state['isCold'])
                        <div x-show="direction === 'sent'">@lang('pages.wallet.sent_transactions')</div>
                    @endunless
                </div>
            </x-slot>

            <div class="p-4">
                <a wire:click="$set('state.direction', 'all');" @click="direction = 'all'" class="dropdown-entry">
                    @lang('pages.wallet.all_transactions')
                </a>

                <a wire:click="$set('state.direction', 'received');" @click="direction = 'received'" class="dropdown-entry">
                    <span>@lang('pages.wallet.received_transactions')</span>

                    <span class="info-badge">{{ $countReceived }}</span>
                </a>

                @unless($state['isCold'])
                    <a wire:click="$set('state.direction', 'sent');" @click="direction = 'sent'" class="dropdown-entry">
                        <span>@lang('pages.wallet.sent_transactions')</span>

                        <span class="info-badge">{{ $countSent }}</span>
                    </a>
                @endunless
            </div>
        </x-ark-dropdown>
    </div>

    <div id="transaction-list" class="w-full">
        <x-skeletons.transactions>
            <x-tables.desktop.transactions :transactions="$transactions" :wallet="$wallet" use-confirmations use-direction />

            <x-tables.mobile.transactions :transactions="$transactions" :wallet="$wallet" use-direction />

            <x-general.pagination :results="$transactions" class="mt-8" />

            <script>
                window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#transaction-list')));
            </script>
        </x-skeletons.transactions>
    </div>
</div>
