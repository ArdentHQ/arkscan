<div>
    <div class="hidden justify-between mb-4 space-x-6 md:flex">
        <x-tabs.wrapper
            class="w-9/12 lg:w-10/12"
            no-data
        >
            <x-tabs.tab name="active" x-bind:tabindex="selected === 'active' || selected === 'monitor' ? 0 : -1">
                <span>@lang('pages.delegates.active')</span>

                @if ($countActive)
                    <span class="info-badge">{{ $countActive }}</span>
                @endif
            </x-tabs.tab>

            <x-tabs.tab name="standby">
                <span>@lang('pages.delegates.standby')</span>

                <span class="info-badge">{{ $countStandby }}</span>
            </x-tabs.tab>

            <x-tabs.tab name="resigned">
                <span>@lang('pages.delegates.resigned')</span>

                <span class="info-badge">{{ $countResigned }}</span>
            </x-tabs.tab>
        </x-tabs.wrapper>

        <div class="relative z-10 w-3/12 text-center rounded-xl lg:w-2/12 dark:bg-black bg-theme-secondary-100">
            <button
                type="button"
                class="flex relative justify-center items-center px-2 w-full font-semibold cursor-pointer transition-default dark:hover:text-theme-secondary-200 hover:text-theme-secondary-900"
                @click="select('monitor')"
                @keydown.enter="select('monitor')"
                @keydown.space.prevent="select('monitor')"
                role="tab"
                id="tab-monitor"
                aria-controls="panel-monitor"
                wire:key="tab-monitor"
                :aria-selected="selected === 'monitor'"
            >
                <span class="flex justify-center items-center space-x-2">
                    <span>
                        <x-ark-icon name="app-monitor" class="text-theme-secondary-700"/>
                    </span>
                    <span
                        class="block pt-4 pb-3 w-full h-full whitespace-nowrap border-b-4"
                        :class="{
                            'border-transparent dark:text-theme-secondary-500 ': selected !== 'monitor',
                            'text-theme-secondary-900 border-theme-primary-600 dark:text-theme-secondary-200 font-semibold': selected === 'monitor',
                        }"
                    >
                        @lang('pages.delegates.monitor')
                    </span>
                </span>
            </button>
        </div>
    </div>

    <div class="md:hidden">
        <x-ark-dropdown
            wrapper-class="relative p-2 mb-8 w-full rounded-xl border border-theme-primary-100 dark:border-theme-secondary-800"
            button-class="p-3 w-full font-semibold text-left text-theme-secondary-900 dark:text-theme-secondary-200"
            dropdown-classes="left-0 w-full z-20"
            :init-alpine="false"
        >
            <x-slot name="button">
                <div class="flex items-center space-x-4">
                    <div>
                        <div x-show="dropdownOpen !== true">
                            <x-ark-icon name="menu" size="sm" />
                        </div>

                        <div x-show="dropdownOpen === true">
                            <x-ark-icon name="menu-show" size="sm" />
                        </div>
                    </div>

                    <div x-show="selected === 'active' && component !== 'monitor'">
                        @lang('pages.delegates.active')
                        @if($countActive)<span class="info-badge">{{ $countActive }}</span>@endif
                    </div>

                    <div x-show="selected === 'standby' && component !== 'monitor'">
                        @lang('pages.delegates.standby')
                        <span class="info-badge">{{ $countStandby }}</span>
                    </div>

                    <div x-show="selected === 'resigned' && component !== 'monitor'">
                        @lang('pages.delegates.resigned')
                        <span class="info-badge">{{ $countResigned }}</span>
                    </div>

                    <div x-show="component === 'monitor'">
                        @lang('pages.delegates.monitor')
                    </div>
                </div>
            </x-slot>

            <div class="py-4">
                <button type="button" @click="select('active')" class="dropdown-entry" :class="{'dropdown-entry-selected' : selected === 'active'}">
                    <span>@lang('pages.delegates.active')</span>

                    @if ($countActive)
                        <span class="info-badge">{{ $countActive }}</span>
                    @endif
                </button>

                <button type="button" @click="select('standby')" class="dropdown-entry" :class="{'dropdown-entry-selected' : selected === 'standby'}">
                    <span>@lang('pages.delegates.standby')</span>

                    <span class="info-badge">{{ $countStandby }}</span>
                </button>

                <button type="button" @click="select('resigned')" class="dropdown-entry" :class="{'dropdown-entry-selected' : selected === 'resigned'}">
                    <span>@lang('pages.delegates.resigned')</span>

                    <span class="info-badge">{{ $countResigned }}</span>
                </button>

                <button type="button" @click="component !== 'monitor' ? select('monitor') : select('active')" class="dropdown-entry" :class="{'dropdown-entry-selected' : selected === 'monitor'}">
                    <span>@lang('pages.delegates.monitor')</span>
                </button>
            </div>
        </x-ark-dropdown>
    </div>
<div>
