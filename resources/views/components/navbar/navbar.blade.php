<div class="h-20"></div>
<div x-data="{ open: false, showSettings: false }" id="navbar" class="fixed z-20 w-full">
    <nav class="relative z-30 bg-white shadow-header-smooth dark:shadow-header-smooth-dark dark:bg-theme-secondary-900">
        <div class="px-8 md:px-10 py-0.5">
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
                    <span class="ml-9 h-5 border-r border-theme-secondary-300 dark:border-theme-secondary-800" aria-hidden="true"></span>

                    {{-- search modal trigger (tablet/desktop) --}}
                    <button
                        type="button"
                        class="hidden items-center p-3 mx-4 rounded sm:flex text-theme-secondary-600 hover:text-theme-primary-500 focus:outline-none transition-default"
                        @click="Livewire.emit('openSearchModal')"
                        dusk="navigation-search-modal-trigger"
                    >
                        <x-ark-icon name="search" />

                        <span class="sr-only">
                            @lang('actions.search')
                        </span>
                    </button>
                </div>

                <div class="flex justify-end">
                    <div class="flex flex-1 justify-end items-center sm:items-stretch sm:justify-between">
                        {{-- Desktop Navbar Items --}}
                        <div class="hidden items-center lg:ml-6 lg:flex">
                            @foreach ($navigation as $navItem)
                                <a
                                    href="{{ route($navItem['route'], $navItem['params'] ?? []) }}"
                                    class="inline-flex items-center px-1 pt-1 font-semibold leading-5 border-b-2
                                        focus:outline-none transition duration-150 ease-in-out h-full
                                        -mb-1
                                        @if(optional(Route::current())->getName() === $navItem['route'])
                                            border-theme-primary-600 text-theme-secondary-900 dark:text-theme-secondary-400
                                        @else
                                            border-transparent text-theme-secondary-700 hover:text-theme-secondary-800 hover:border-theme-secondary-300 dark:text-theme-secondary-500 dark:hover:text-theme-secondary-400
                                        @endif
                                        @if(!$loop->first) ml-8 @endif"
                                >
                                    {{ $navItem['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex inset-y-0 right-0 items-center pr-2 sm:static sm:inset-auto sm:ml-4 sm:pr-0">
                        {{-- Mobile Hamburger icon --}}
                        <div class="flex items-center lg:hidden">
                            <button @click="open = !open" class="inline-flex justify-center items-center rounded-md transition duration-150 ease-in-out text-theme-secondary-900 dark:text-theme-secondary-600">
                                <span :class="{'hidden': open, 'inline-flex': !open }">
                                    <x-ark-icon name="menu" size="sm" />
                                </span>

                                <span :class="{'hidden': !open, 'inline-flex': open }" x-cloak>
                                    <x-ark-icon name="menu-show" size="sm" />
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center ml-6 md:hidden">
                        <div class="pl-8 border-l border-theme-primary-100 text-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-800">
                            <button
                                type="button"
                                @click="Livewire.emit('openSearchModal')"
                                class="inline-flex justify-center items-center py-2 rounded-md transition duration-150 ease-in-out text-theme-primary-300 dark:text-theme-secondary-600"
                            >
                                <span class="inline-flex"><x-ark-icon name="search" size="sm" /></span>
                            </button>
                        </div>
                    </div>

                    @if(Network::canBeExchanged())
                        <div class="hidden items-center ml-6 md:flex lg:ml-8">
                            <div class="pl-8 font-semibold border-l border-theme-primary-100 text-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-800">
                                <livewire:price-ticker />
                            </div>
                        </div>
                    @endif

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
                        <div class="flex py-3 px-8 mt-2 -mb-4 font-semibold bg-theme-secondary-100 text-theme-secondary-900 dark:text-theme-secondary-300 dark:bg-theme-secondary-800">
                            <livewire:price-ticker />
                        </div>
                    @endif
                </div>
            </div>
        </template>
    </nav>
</div>
