<div class="justify-between hidden md:flex">
    <div class="flex w-10/12 tabs">
        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': status === 'active' }"
            wire:click="$emit('filterByDelegateStatus', 'active');"
            @click="component = 'list'; status = 'active'"
        >
            <span>@lang('pages.monitor.active')</span>

            @if ($countActive)
                <span class="info-badge">{{ $countActive }}</span>
            @endif
        </div>

        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': status === 'standby' }"
            wire:click="$emit('filterByDelegateStatus', 'standby');"
            @click="component = 'list'; status = 'standby'"
        >
            <span>@lang('pages.monitor.standby')</span>

            <span class="info-badge">{{ $countStandby }}</span>
        </div>

        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': status === 'resigned' }"
            wire:click="$emit('filterByDelegateStatus', 'resigned');"
            @click="component = 'list'; status = 'resigned'"
        >
            <span>@lang('pages.monitor.resigned')</span>

            <span class="info-badge">{{ $countResigned }}</span>
        </div>
    </div>

    <div class="w-2/12 text-center tabs md:ml-6">
        {{-- @TODO: svg icon --}}
        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': component === 'monitor' }"
            @click="component === 'monitor' ? component = 'list' : component = 'monitor'"
        >
            @lang('pages.monitor.title')
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

                <div x-show="status === 'active'">@lang('pages.monitor.active')</div>
                <div x-show="status === 'standby'">@lang('pages.monitor.standby')</div>
                <div x-show="status === 'resigned'">@lang('pages.monitor.resigned')</div>
            </div>
        </x-slot>

        <div class="p-4">
            <a wire:click="$emit('filterByDelegateStatus', 'active');" @click="component = 'list'; status = 'active'" class="dropdown-entry">
                <span>@lang('pages.monitor.active')</span>

                @if ($countActive)
                    <span class="info-badge">{{ $countActive }}</span>
                @endif
            </a>

            <a wire:click="$emit('filterByDelegateStatus', 'standby');" @click="component = 'list'; status = 'standby'" class="dropdown-entry">
                <span>@lang('pages.monitor.standby')</span>

                <span class="info-badge">{{ $countStandby }}</span>
            </a>

            <a wire:click="$emit('filterByDelegateStatus', 'resigned');" @click="component = 'list'; status = 'resigned'" class="dropdown-entry">
                <span>@lang('pages.monitor.resigned')</span>

                <span class="info-badge">{{ $countResigned }}</span>
            </a>
        </div>
    </x-ark-dropdown>
</div>
