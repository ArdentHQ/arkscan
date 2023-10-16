<header class="flex flex-col md:hidden">
    <div
        class="fixed z-20 w-full md:relative"
        x-data="Navbar.dropdown({
            dark: window.getThemeMode() === 'dark',
            open: false,
            showSettings: false,

            lockBody() {
                return ! this.isIOSSafari() && window.innerWidth <= this.lockBodyBreakpoint;
            },

            isIOSSafari() {
                if (! /(Macintosh)|(Mac OS)|(iPad)|(iPod)|(iPhone)/.test(window.navigator.userAgent)) {
                    return false;
                }

                if (! /(Safari)/.test(window.navigator.userAgent)) {
                    return false;
                }

                if (! /(AppleWebKit)/.test(window.navigator.userAgent)) {
                    return false;
                }

                return ('ontouchstart' in window) || (navigator.MaxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0);
            },
        })"
        @theme-changed.window="dark = $event.detail.theme === 'dark'"
    >
        <div
            x-show="openDropdown !== null || open"
            class="overflow-y-auto fixed inset-0 z-30 md:relative"
            @click="openDropdown = null; open = false;"
            x-cloak
        ></div>

        <nav
            x-ref="nav"
            class="relative z-30 bg-white border-b border-theme-secondary-300 dark:bg-theme-secondary-900 dark:border-theme-secondary-800"
            @click.outside="open = false"
        >
            <div class="flex relative justify-between w-full sm:h-16 h-[3.25rem] content-container">
                <div class="flex flex-shrink-0 items-center">
                    <a class="flex items-center" href="{{ route('home') }}">
                        <x-navbar.logo />
                    </a>
                </div>

                <div class="flex justify-end">
                    <div class="flex justify-end">
                        <div class="flex items-center space-x-1">
                            <x-navbar.button
                                @click="Livewire.emit('openSearchModal')"
                                dusk="navigation-search-modal-trigger"
                                :disabled="app()->isDownForMaintenance()"
                            >
                                <x-ark-icon name="magnifying-glass" />

                                <span class="sr-only">
                                    @lang('actions.search')
                                </span>
                            </x-navbar.button>

                            <x-navbar.button @click="toggle">
                                <span :class="{'hidden': open, 'inline-flex': !open }">
                                    <x-ark-icon name="menu" />
                                </span>

                                <span :class="{'hidden': !open, 'inline-flex': open }" x-cloak>
                                    <x-ark-icon name="menu-show" />
                                </span>
                            </x-navbar.button>
                        </div>
                    </div>
                </div>
            </div>

            <div
                x-show="open"
                x-transition.opacity
                x-cloak
            >
                <div class="border-t-2 shadow-xl border-theme-secondary-200 dark:border-theme-secondary-800">
                    <div class="pt-2 bg-white rounded-b-lg dark:bg-theme-secondary-800">
                        @foreach ($navigation as $navItem)
                            @if (Arr::exists($navItem, 'children'))
                                <div class="relative h-full dark:bg-theme-secondary-800">
                                    <a
                                        href="#"
                                        class="inline-flex relative justify-between items-center py-3 px-6 w-full h-full font-semibold leading-5 focus:ring-inset focus:outline-none text-theme-secondary-700 dark:text-theme-secondary-200 hover:text-theme-secondary-800"
                                        @click="openDropdown = openDropdown === '{{ $navItem['label'] }}' ? null : '{{ $navItem['label'] }}'"
                                    >
                                        <span :class="{ 'text-theme-secondary-700 dark:text-theme-secondary-200': openDropdown === '{{ $navItem['label'] }}' }">{{ $navItem['label'] }}</span>
                                        <span class="ml-2 text-theme-secondary-700 dark:text-theme-secondary-400" :class="{ 'rotate-180': openDropdown === '{{ $navItem['label'] }}' }">
                                            <x-ark-icon name="arrows.chevron-down-small" size="xs" />
                                        </span>
                                    </a>

                                    <div
                                        x-show="openDropdown === '{{ $navItem['label'] }}'"
                                        class="bg-white dark:bg-theme-secondary-800"
                                        x-cloak
                                    >
                                        <div class="flex flex-col pt-2 pb-2 w-full">
                                            @foreach ($navItem['children'] as $menuItem)
                                                <x-navbar.mobile.list-item
                                                    :route="$menuItem['route'] ?? null"
                                                    :params="$menuItem['params'] ?? null"
                                                    :label="$menuItem['label']"
                                                    :url="$menuItem['url'] ?? null"
                                                />
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <x-navbar.mobile.item
                                    :route="$navItem['route']"
                                    :params="$navItem['params'] ?? null"
                                    :label="$navItem['label']"
                                />
                            @endif
                        @endforeach

                        <div class="py-4 dark:bg-black bg-theme-secondary-100">
                            <div class="mx-6 space-y-3 divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-secondary-800">
                                <x-navbar.mobile.setting-item title="{{ trans('general.select_theme') }}">
                                    <livewire:navbar.dark-mode-toggle
                                        active-icon="underline-moon"
                                        inactive-icon="underline-sun"
                                        setting="darkTheme"
                                        mobile
                                    />
                                </x-navbar.mobile.setting-item>

                                <x-navbar.mobile.setting-item
                                    title="{{ trans('general.select_network') }}"
                                    class="pt-3"
                                >
                                    <x-navbar.network-dropdown />
                                </x-navbar.mobile.setting-item>

                                <div class="flex pt-3 font-semibold dark:text-theme-secondary-500">
                                    <livewire:price-ticker />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>
