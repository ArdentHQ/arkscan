<div class="mb-1 h-20"></div>
<div
    id="navbar"
    class="fixed z-20 w-full"
    x-data="Navbar.dropdown({ open: false, showSettings: false })"
    x-init="init"
>
    <nav x-ref="nav" class="relative z-30 bg-white border-b border-theme-secondary-300 dark:bg-theme-secondary-900 dark:border-theme-secondary-800">
        <div class="py-0.5 px-8 md:px-10">
            <div class="flex relative justify-between h-20">

                {{-- LOGO --}}
                <div class="flex flex-shrink-0 items-center">
                    <a class="flex items-center" href="{{ route('home') }}">
                        @if($logo ?? false)
                            {{ $logo }}
                        @else
                            <x-ark-icon name="ark-logo-red-square" size="xxl" />

                            <div class="hidden ml-6 text-lg lg:block"><span class="font-black text-theme-secondary-900">ARK</span> {{ $title }}</div>
                        @endif
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
                        <x-ark-icon name="search" />

                        <span class="sr-only">
                            @lang('actions.search')
                        </span>
                    </x-navbar.button>
                </div>

                <div class="flex justify-end">
                    <div class="flex flex-1 justify-end items-center sm:justify-between sm:items-stretch">
                        {{-- Desktop Navbar Items --}}
                        <div class="hidden items-center -mx-4 lg:flex">
                            @foreach ($navigation as $navItem)
                                <a
                                    href="{{ route($navItem['route'], $navItem['params'] ?? []) }}"
                                    class="inline-flex font-semibold leading-5 group
                                        focus:outline-none transition duration-150 ease-in-out h-full px-2 mx-2 relative border-t-2 border-transparent rounded
                                        @if(optional(Route::current())->getName() === $navItem['route'])
                                            text-theme-secondary-900 dark:text-theme-secondary-400
                                        @else
                                            text-theme-secondary-700 hover:text-theme-secondary-800 dark:text-theme-secondary-500 dark:hover:text-theme-secondary-400
                                        @endif
                                    "
                                >
                                    <span class="flex items-center w-full h-full mt-0.5 border-b-2  @if(optional(Route::current())->getName() === $navItem['route']) border-theme-primary-600 @else border-transparent group-hover:border-theme-secondary-300 @endif">
                                        <span class="-mt-0.5">{{ $navItem['label'] }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if(Network::canBeExchanged())
                        <div class="hidden items-center md:flex">
                            <x-navbar.separator class="md:hidden lg:inline" />

                            <div class="hidden font-semibold md:flex lg:pl-8 dark:text-white text-theme-secondary-900">
                                <livewire:price-ticker />
                            </div>
                        </div>
                    @endif


                    <div class="flex items-center -mr-5 md:-mr-8 lg:hidden">
                        @if(Network::canBeExchanged())
                            <x-navbar.separator class="hidden md:inline" />
                        @endif

                        {{-- Mobile Hamburger icon --}}
                        <x-navbar.button
                            @click="open = !open"
                            margin-class="ml-1 ml-4 -mr-3 md:mr-4"
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
                            <x-ark-icon name="search" />

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
            <div class="border-t-2 shadow-xl lg:hidden border-theme-secondary-200 dark:border-theme-secondary-800" @click.away="open = false">
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
