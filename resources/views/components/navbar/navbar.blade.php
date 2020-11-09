<div class="h-20 lg:h-24"></div>
<div x-data="{ open: false, openDropdown: null, selectedChild: null }" id="navbar" class="fixed z-20 w-full">
    <div
        x-show="openDropdown !== null || open"
        class="fixed inset-0 z-30 overflow-y-auto bg-theme-secondary-900"
        :class="{
            'opacity-75': openDropdown !== 'settings',
            'opacity-0': openDropdown === 'settings',
        }"
        x-cloak
        @click="openDropdown = null; open = false;"
    ></div>

    <nav class="relative z-30 bg-white shadow-header-smooth dark:shadow-none dark:bg-theme-secondary-900">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="relative flex justify-between h-20 lg:h-24">

                {{-- LOGO --}}
                <div class="flex items-center flex-shrink-0">
                    <a class="flex items-center" href="{{ route('home') }}">
                        @if($logo ?? false)
                            {{ $logo }}
                        @else
                            <x-ark-icon name="ark-logo-red-square" size="xxl" />

                            <div class="hidden ml-6 text-lg lg:block"><span class="font-black text-theme-secondary-900">ARK</span> {{ $title }}</div>
                        @endif
                    </a>
                </div>

                <div class="flex justify-end">
                    <div class="flex items-center justify-end flex-1 sm:items-stretch sm:justify-between">
                        {{-- Desktop Navbar Items --}}
                        <div class="items-center hidden lg:ml-6 lg:flex">
                            @foreach ($navigation as $navItem)
                                @if(isset($navItem['children']))
                                    <a
                                        href="#"
                                        class="relative inline-flex justify-center items-center px-1 pt-1 font-semibold leading-5 border-b-2 border-transparent text-theme-secondary-700 hover:text-theme-secondary-800 hover:border-theme-secondary-300 focus:outline-none transition duration-150 ease-in-out h-full dark:text-theme-secondary-500 dark:hover:text-theme-secondary-400
                                            @if(!$loop->first) ml-8 @endif"
                                        @click="openDropdown = openDropdown === '{{ $navItem['label'] }}' ? null : '{{ $navItem['label'] }}'"
                                    >
                                        <span :class="{ 'text-theme-primary-600': openDropdown === '{{ $navItem['label'] }}' }">{{ $navItem['label'] }}</span>
                                        <span class="ml-2 transition duration-150 ease-in-out text-theme-primary-600" :class="{ 'rotate-180': openDropdown === '{{ $navItem['label'] }}' }"><x-ark-icon name="chevron-down" size="xs" /></span>
                                    </a>
                                    <div x-show="openDropdown === '{{ $navItem['label'] }}'" class="absolute top-0 right-0 z-30 pb-8 mt-24 bg-white rounded-b-lg" x-cloak>
                                        <div class="pb-8 mx-8 border-t border-theme-secondary-200"></div>
                                        <div class="flex">
                                            <div class="flex-shrink-0 w-56 border-r border-theme-secondary-300">
                                                @foreach ($navItem['children'] as $childNavItem)
                                                    <div @mouseenter="selectedChild = {{ json_encode($childNavItem) }}">
                                                        <x-ark-sidebar-link :route="$childNavItem['route']" :name="$childNavItem['label']" :params="$childNavItem['params'] ?? []"/>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="flex flex-col flex-shrink-0 pl-8 pr-8 w-128">
                                                <img class="w-full" :src="selectedChild ? selectedChild.image : '{{ $navItem['image'] }}'" />

                                                <template x-if="selectedChild">
                                                    <span x-text="selectedChild.label" class="mb-2 text-xl font-semibold text-theme-secondary-900"></span>
                                                    <span x-text="selectedChild.description"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a
                                        href="{{ route($navItem['route'], $navItem['params'] ?? []) }}"
                                        class="inline-flex items-center px-1 pt-1 font-semibold leading-5 border-b-2
                                            focus:outline-none transition duration-150 ease-in-out h-full
                                            @if(optional(Route::current())->getName() === $navItem['route'])
                                                border-theme-primary-600 text-theme-secondary-900 dark:text-theme-secondary-400
                                            @else
                                                border-transparent text-theme-secondary-700 hover:text-theme-secondary-800 hover:border-theme-secondary-300 dark:text-theme-secondary-500 dark:hover:text-theme-secondary-400
                                            @endif
                                            @if(!$loop->first) ml-8 @endif"
                                        @click="openDropdown = null;"
                                    >
                                        {{ $navItem['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-4 sm:pr-0">
                        {{-- Mobile Hamburger icon --}}
                        <div class="flex items-center lg:hidden">
                            <button @click="open = !open" class="inline-flex items-center justify-center p-2 transition duration-150 ease-in-out rounded-md text-theme-secondary-900">
                                <span :class="{'hidden': open, 'inline-flex': !open }"><x-ark-icon name="menu-open" size="sm" /></span>
                                <span :class="{'hidden': !open, 'inline-flex': open }" x-cloak><x-ark-icon name="menu-close" size="sm" /></span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center ml-6 md:hidden">
                        <div class="pl-8 border-l border-theme-primary-100 text-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-800">
                            <button
                                @click="$dispatch('mobile-search')"
                                class="inline-flex items-center justify-center py-2 transition duration-150 ease-in-out rounded-md text-theme-primary-300"
                            >
                                <span class="inline-flex"><x-ark-icon name="search" size="sm" /></span>
                            </button>
                        </div>
                    </div>

                    @if(Network::canBeExchanged())
                        <div class="items-center hidden ml-6 md:flex lg:ml-8">
                            <div class="pl-8 font-semibold border-l border-theme-primary-100 text-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-800">
                                <livewire:price-ticker />
                            </div>
                        </div>
                    @endif

                    <livewire:navbar-settings />
                </div>
            </div>
        </div>

        {{-- Mobile dropdown --}}
        <div :class="{'block': open, 'hidden': !open}" class="border-t-2 lg:hidden border-theme-secondary-200">
            <div class="pt-2 pb-4 rounded-b-lg">
                @foreach ($navigation as $navItem)
                    @if(isset($navItem['children']))
                        <div class="flex w-full">
                            <div class="z-10 w-2 -mr-1"></div>
                            <a
                                href="#"
                                class="flex items-center justify-between w-full px-8 py-3 font-semibold border-l-2 border-transparent"
                                @click="openDropdown = openDropdown === '{{ $navItem['label'] }}' ? null : '{{ $navItem['label'] }}'"
                            >
                                <span :class="{ 'text-theme-primary-600': openDropdown === '{{ $navItem['label'] }}' }">{{ $navItem['label'] }}</span>
                                <span class="ml-2 transition duration-150 ease-in-out text-theme-primary-600" :class="{ 'rotate-180': openDropdown === '{{ $navItem['label'] }}' }"><x-ark-icon name="chevron-down" size="xs" /></span>
                            </a>
                        </div>
                        <div x-show="openDropdown === '{{ $navItem['label'] }}'" class="pl-8" x-cloak>
                            @foreach ($navItem['children'] as $childNavItem)
                                <div @mouseenter="selectedChild = {{ json_encode($childNavItem) }}">
                                    <x-ark-sidebar-link :route="$childNavItem['route']" :name="$childNavItem['label']" :params="$childNavItem['params'] ?? []" />
                                </div>
                            @endforeach
                        </div>
                    @else
                        <x-ark-sidebar-link :route="$navItem['route']" :name="$navItem['label']" :params="$navItem['params'] ?? []" />
                    @endif
                @endforeach
            </div>
        </div>
    </nav>
</div>
