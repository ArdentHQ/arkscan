<header class="flex flex-col">
    <div class="mb-1 h-20"></div>
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
            <div class="px-8 md:px-10">
                <div class="flex relative justify-between h-21">

                    {{-- LOGO --}}
                    <div class="flex flex-shrink-0 items-center">
                        <a class="flex items-center" href="{{ route('home') }}">
                            {{ $logo }}
                        </a>
                    </div>

                    <div class="hidden items-center mr-auto md:flex">
                        <x-navbar.separator />

                        {{-- search modal trigger (tablet/desktop) --}}
                        <x-navbar.button
                            class="hidden sm:flex"
                            @click="Livewire.emit('openSearchModal')"
                            dusk="navigation-search-modal-trigger"
                        >
                            <x-ark-icon name="magnifying-glass" />

                            <span class="sr-only">
                                @lang('actions.search')
                            </span>
                        </x-navbar.button>
                    </div>

                    <div class="flex justify-end">
                        <div class="flex flex-1 justify-end items-center sm:justify-between sm:items-stretch">
                            {{-- Desktop Navbar Items --}}
                            <div class="hidden items-center -mx-4 xl:flex">
                                @foreach ($navigation as $navItem)
                                    <x-navbar.item
                                        :route="$navItem['route']"
                                        :params="$navItem['params'] ?? null"
                                        :label="$navItem['label']"
                                    />
                                @endforeach
                            </div>
                        </div>

                        @if(Network::canBeExchanged())
                            <div class="hidden items-center md:flex">
                                <x-navbar.separator class="md:hidden xl:inline" />

                                <div class="hidden font-semibold md:flex xl:pl-8 dark:text-white text-theme-secondary-900">
                                    <livewire:price-ticker />
                                </div>
                            </div>
                        @endif


                        <div class="flex items-center -mr-5 md:-mr-8 xl:hidden">
                            @if(Network::canBeExchanged())
                                <x-navbar.separator class="hidden md:inline" />
                            @endif

                            {{-- Mobile Hamburger icon --}}
                            <x-navbar.button
                                @click="toggle"
                                margin-class="ml-4 -mr-4 md:mr-4"
                            >
                                <span :class="{'hidden': open, 'inline-flex': !open }">
                                    <x-ark-icon name="menu" />
                                </span>

                                <span :class="{'hidden': !open, 'inline-flex': open }" x-cloak>
                                    <x-ark-icon name="menu-show" />
                                </span>
                            </x-navbar.button>

                            <x-navbar.separator class="md:hidden" />

                            <x-navbar.button
                                class="md:hidden"
                                @click="Livewire.emit('openSearchModal')"
                                dusk="navigation-search-modal-trigger"
                            >
                                <x-ark-icon name="magnifying-glass" />

                                <span class="sr-only">
                                    @lang('actions.search')
                                </span>
                            </x-navbar.button>
                        </div>


                        <livewire:navbar-settings />
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
