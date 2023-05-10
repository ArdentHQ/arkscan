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
                                <x-navbar.item
                                    :route="$navItem['route']"
                                    :params="$navItem['params'] ?? null"
                                    :label="$navItem['label']"
                                />
                            @endforeach
                        </div>
                    </div>

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

            <template x-if="open">
                <div class="border-t-2 shadow-xl xl:hidden border-theme-secondary-200 dark:border-theme-secondary-800">
                    <div class="pt-2 pb-4 rounded-b-lg">
                        @foreach ($navigation as $navItem)
                            <x-ark-navbar-link-mobile :route="$navItem['route']" :name="$navItem['label']" :params="$navItem['params'] ?? []" />
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
