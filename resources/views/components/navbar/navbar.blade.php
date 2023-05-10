<header class="flex flex-col">
    <div class="mb-1 h-15"></div>
    <div
        id="navbar"
        class="fixed z-20 w-full"
        x-data="Navbar.dropdown({
            dark: window.getThemeMode() === 'dark',
            open: false,
            showSettings: false
        })"
        @theme-changed.window="dark = $event.detail.theme === 'dark'"
    >
        <div x-show="openDropdown !== null || open" class="overflow-y-auto fixed inset-0 z-30" x-cloak @click="openDropdown = null; open = false;"></div>

        <nav
            x-ref="nav"
            class="relative z-30 bg-white border-b border-theme-secondary-300 dark:bg-theme-secondary-900 dark:border-theme-secondary-800"
            @click.outside="open = false"
        >
            <div class="flex relative justify-between w-full h-16 content-container">
                {{-- LOGO --}}
                <div class="flex flex-shrink-0 items-center">
                    <a class="flex items-center" href="{{ route('home') }}">
                        {{ $logo }}
                    </a>
                </div>

                <div class="flex justify-end">
                    <div class="flex flex-1 justify-end items-center sm:justify-between sm:items-stretch">
                        {{-- Desktop Navbar Items --}}
                        <div class="hidden items-center -mx-4 md:flex">
                            @foreach ($navigation as $navItem)
                                @if (Arr::exists($navItem, 'children'))
                                    <div class="relative h-full">
                                        <a
                                            href="#"
                                            class="inline-flex relative justify-center items-center px-1 pt-px mr-6 h-full font-semibold leading-5 border-b-2 border-transparent transition duration-150 ease-in-out focus:outline-none text-theme-secondary-700 dark:text-theme-secondary-400 hover:text-theme-secondary-800 hover:border-theme-secondary-300"
                                            @click="openDropdown = openDropdown === '{{ $navItem['label'] }}' ? null : '{{ $navItem['label'] }}'"
                                        >
                                            <span :class="{ 'text-theme-secondary-700 dark:text-theme-secondary-200': openDropdown === '{{ $navItem['label'] }}' }">{{ $navItem['label'] }}</span>
                                            <span class="ml-2 transition duration-150 ease-in-out text-theme-secondary-700 dark:text-theme-secondary-400" :class="{ 'rotate-180': openDropdown === '{{ $navItem['label'] }}' }">
                                                <x-ark-icon name="arrows.chevron-down-small" size="xs" />
                                            </span>
                                        </a>

                                        <div
                                            x-show="openDropdown === '{{ $navItem['label'] }}'"
                                            class="absolute z-30 max-w-4xl bg-white rounded-lg shadow-lg top-[5rem] dark:bg-theme-secondary-800"
                                            x-transition.origin.top
                                            x-cloak
                                        >
                                            <div class="flex flex-col pt-2 pb-2 w-60">
                                                @foreach ($navItem['children'] as $menuItem)
                                                    <x-navbar.list-item
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
                                    <x-navbar.item
                                        :route="$navItem['route']"
                                        :params="$navItem['params'] ?? null"
                                        :label="$navItem['label']"
                                    />
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <div class="flex items-center space-x-1 md:hidden">
                            <x-navbar.button
                                @click="Livewire.emit('openSearchModal')"
                                dusk="navigation-search-modal-trigger"
                            >
                                <x-ark-icon name="magnifying-glass" />

                                <span class="sr-only">
                                    @lang('actions.search')
                                </span>
                            </x-navbar.button>

                            {{-- Mobile Hamburger icon --}}
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

            <template x-if="open">
                <div class="border-t-2 shadow-xl md:hidden border-theme-secondary-200 dark:border-theme-secondary-800">
                    <div class="pt-2 pb-4 bg-white rounded-b-lg dark:bg-theme-secondary-800">
                        @foreach ($navigation as $navItem)
                            @if (Arr::exists($navItem, 'children'))
                                <div class="relative h-full dark:bg-theme-secondary-800">
                                    <a
                                        href="#"
                                        class="inline-flex relative justify-between items-center py-3 px-6 w-full h-full font-semibold leading-5 focus:outline-none text-theme-secondary-700 dark:text-theme-secondary-400 hover:text-theme-secondary-800"
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


                        @if(Network::canBeExchanged())
                            <div class="flex py-3 px-8 mt-2 -mb-4 font-semibold md:hidden dark:text-white bg-theme-secondary-100 text-theme-secondary-900 dark:bg-theme-secondary-800">
                                <livewire:price-ticker />
                            </div>
                        @endif
                    </div>
                </div>
            </template>
        </nav>
    </div>
</header>
