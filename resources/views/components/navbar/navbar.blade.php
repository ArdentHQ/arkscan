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
        <div x-show="openDropdown !== null || open" class="fixed inset-0 z-30 overflow-y-auto" x-cloak @click="openDropdown = null; open = false;"></div>
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

                    <div class="flex justify-end">
                        <div class="flex flex-1 justify-end items-center sm:justify-between sm:items-stretch">
                            {{-- Desktop Navbar Items --}}
                            <div class="hidden items-center -mx-4 xl:flex">
                                @foreach ($navigation as $navItem)
                                    @if (Arr::exists($navItem, 'children'))
                                        <div class="relative h-full">
                                            <a
                                                href="#"
                                                class="relative inline-flex items-center justify-center h-full px-1 pt-px mr-8 font-semibold leading-5 transition duration-150 ease-in-out border-b-2 border-transparent text-theme-secondary-700 hover:text-theme-secondary-800 hover:border-theme-secondary-300 focus:outline-none"
                                                @click="openDropdown = openDropdown === '{{ $navItem['label'] }}' ? null : '{{ $navItem['label'] }}'"
                                            >
                                                <span :class="{ 'text-theme-secondary-700': openDropdown === '{{ $navItem['label'] }}' }">{{ $navItem['label'] }}</span>
                                                <span class="ml-2 transition duration-150 ease-in-out text-theme-secondary-700" :class="{ 'rotate-180': openDropdown === '{{ $navItem['label'] }}' }">
                                                    <x-ark-icon name="arrows.chevron-down-small" size="xs" />
                                                </span>
                                            </a>

                                            <div
                                                x-show="openDropdown === '{{ $navItem['label'] }}'"
                                                class="absolute top-[5.5rem] z-30 max-w-4xl bg-white rounded-lg shadow-lg"
                                                x-transition.origin.top
                                                x-cloak
                                            >
                                                <div class="flex flex-col w-60 pt-2 pb-2">
                                                    @foreach ($navItem['children'] as $menuItem)
                                                        <x-navbar.list-item
                                                            :route="$menuItem['route']"
                                                            :params="$menuItem['params'] ?? null"
                                                            :label="$menuItem['label']"
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


                                {{-- <a
                                href="#"
                                class="relative inline-flex items-center justify-center h-full px-1 pt-px mr-8 font-semibold leading-5 transition duration-150 ease-in-out border-b-2 border-transparent text-theme-secondary-700 hover:text-theme-secondary-800 hover:border-theme-secondary-300 focus:outline-none"
                                @click="openDropdown = openDropdown === 'products' ? null : 'products'"
                            >
                                <span :class="{ 'text-theme-secondary-700': openDropdown === 'products' }">@lang('menus.documentation.title')</span>
                                <span class="ml-2 transition duration-150 ease-in-out text-theme-secondary-700" :class="{ 'rotate-180': openDropdown === 'products' }">
                                    <x-ark-icon name="arrows.chevron-down-small" size="xs" />
                                </span>
                            </a>
                            <div
                                x-show="openDropdown === 'products'"
                                class="absolute top-0 z-30 max-w-4xl mt-21 bg-white rounded-b-lg"
                                x-transition.origin.top
                                x-cloak
                            >
                                <div class="flex flex-col w-72 pt-2 pb-6">
                                    @foreach ($productsMenu as $menuItem)
                                        <x-product-link :name="$menuItem['name']" :slug="$menuItem['slug']" :coming-soon="$menuItem['is_coming_soon']">
                                            <x-slot name="customIcon">
                                                <x-ark-icon name="navbar-{{ $menuItem['slug'] }}" size="lg" />
                                            </x-slot>
                                        </x-product-link>
                                    @endforeach
                                    <div class="mx-6 my-2">
                                        <hr class="bg-theme-secondary-300 border-theme-secondary-300" />
                                    </div>
                                    @foreach ($quickAccessMenu as $menuItem)
                                        <x-product-link :name="$menuItem['name']" :slug="$menuItem['slug']" :coming-soon="$menuItem['is_coming_soon']">
                                            <x-slot name="customIcon">
                                                <x-ark-icon name="navbar-{{ $menuItem['slug'] }}" size="lg" />
                                            </x-slot>
                                        </x-product-link>
                                    @endforeach
                                </div>
                            </div> --}}

                            {{-- Rest of the navigation items --}}
                            {{-- @foreach ($navigation as $navItem)
                                <a
                                    href="{{ route($navItem['route'], $navItem['params'] ?? []) }}"
                                    class="inline-flex items-center px-1 pt-px font-semibold leading-5 border-b-2 @if(Request::path() === $navItem['path']) border-theme-secondary-700 text-theme-secondary-900 @else border-transparent text-theme-secondary-700 hover:text-theme-secondary-800 hover:border-theme-secondary-300 @endif focus:outline-none transition duration-150 ease-in-out h-full @if(!$loop->first) ml-8 @endif"
                                    @click="openDropdown = null;"
                                >
                                    {{ $navItem['label'] }}
                                </a>
                            @endforeach  --}}
                            </div>
                        </div>

                        <div class="flex items-center -mr-5 md:-mr-8 xl:hidden">
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
                        {{-- @foreach ($navigation as $navItem)
                            <x-ark-navbar-link-mobile :route="$navItem['route']" :name="$navItem['label']" :params="$navItem['params'] ?? []" />
                        @endforeach --}}

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
