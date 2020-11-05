<div class="justify-between hidden md:flex">
    <div class="flex w-10/12 tabs">
        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': status === 'active' && component !== 'monitor' }"
            wire:click="$emit('filterByDelegateStatus', 'active');"
            @click="component = 'table'; status = 'active'"
        >
            <span>@lang('pages.delegates.active')</span>

            @if ($countActive)
                <span class="info-badge">{{ $countActive }}</span>
            @endif
        </div>

        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': status === 'standby' && component !== 'monitor' }"
            wire:click="$emit('filterByDelegateStatus', 'standby');"
            @click="component = 'table'; status = 'standby'"
        >
            <span>@lang('pages.delegates.standby')</span>

            <span class="info-badge">{{ $countStandby }}</span>
        </div>

        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': status === 'resigned' && component !== 'monitor' }"
            wire:click="$emit('filterByDelegateStatus', 'resigned');"
            @click="component = 'table'; status = 'resigned'"
        >
            <span>@lang('pages.delegates.resigned')</span>

            <span class="info-badge">{{ $countResigned }}</span>
        </div>
    </div>

    <div class="w-2/12 text-center tabs md:ml-6">
        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': component === 'monitor' }"
            @click="component === 'monitor' ? component = 'table' : component = 'monitor'"
        >
            <div class="flex justify-center space-x-2">
                <span>@svg('app-monitor', 'w-5 h-5 text-theme-secondary-700')</span>
                <span>@lang('pages.delegates.monitor')</span>
            </div>
        </div>
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
                <x-icon name="menu-open" size="sm" />

                <div x-show="status === 'active'">@lang('pages.delegates.active')</div>
                <div x-show="status === 'standby'">@lang('pages.delegates.standby')</div>
                <div x-show="status === 'resigned'">@lang('pages.delegates.resigned')</div>
            </div>
        </x-slot>

        <div class="p-4">
            <a wire:click="$emit('filterByDelegateStatus', 'active');" @click="component = 'table'; status = 'active'" class="dropdown-entry">
                <span>@lang('pages.delegates.active')</span>

                @if ($countActive)
                    <span class="info-badge">{{ $countActive }}</span>
                @endif
            </a>

            <a wire:click="$emit('filterByDelegateStatus', 'standby');" @click="component = 'table'; status = 'standby'" class="dropdown-entry">
                <span>@lang('pages.delegates.standby')</span>

                <span class="info-badge">{{ $countStandby }}</span>
            </a>

            <a wire:click="$emit('filterByDelegateStatus', 'resigned');" @click="component = 'table'; status = 'resigned'" class="dropdown-entry">
                <span>@lang('pages.delegates.resigned')</span>

                <span class="info-badge">{{ $countResigned }}</span>
            </a>
        </div>
    </x-ark-dropdown>
</div>
