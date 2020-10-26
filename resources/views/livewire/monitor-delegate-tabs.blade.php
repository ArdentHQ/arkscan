<div class="hidden tabs md:flex">
    <div
        class="tab-item transition-default"
        :class="{ 'tab-item-current': status === 'active' }"
        wire:click="$emit('filterByDelegateStatus', 'active');"
        @click="status = 'active'"
    >
        @lang('pages.monitor.active') {{ $countActive }}
    </div>

    <div
        class="tab-item transition-default"
        :class="{ 'tab-item-current': status === 'standby' }"
        wire:click="$emit('filterByDelegateStatus', 'standby');"
        @click="status = 'standby'"
    >
        @lang('pages.monitor.standby') {{ $countStandby }}
    </div>

    <div
        class="tab-item transition-default"
        :class="{ 'tab-item-current': status === 'resigned' }"
        wire:click="$emit('filterByDelegateStatus', 'resigned');"
        @click="status = 'resigned'"
    >
        @lang('pages.monitor.resigned') {{ $countResigned }}
    </div>
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
                @svg('menu-open', 'h-4 w-4')

                <div x-show="status === 'active'">@lang('pages.monitor.active')</div>
                <div x-show="status === 'standby'">@lang('pages.monitor.standby')</div>
                <div x-show="status === 'resigned'">@lang('pages.monitor.resigned')</div>
            </div>
        </x-slot>

        <div class="p-4">
            <a wire:click="$emit('filterByDelegateStatus', 'active');" @click="status = 'active'" class="dropdown-entry">
                @lang('pages.wallet.received_transactions') {{ $countActive }}
            </a>

            <a wire:click="$emit('filterByDelegateStatus', 'standby');" @click="status = 'standby'" class="dropdown-entry">
                @lang('pages.wallet.received_transactions') {{ $countStandby }}
            </a>

            <a wire:click="$emit('filterByDelegateStatus', 'resigned');" @click="status = 'resigned'" class="dropdown-entry">
                @lang('pages.wallet.received_transactions') {{ $countResigned }}
            </a>
        </div>
    </x-ark-dropdown>

    {{-- @TODO: toggle monitor button --}}
</div>
