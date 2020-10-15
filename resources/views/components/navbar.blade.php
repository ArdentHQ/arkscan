<div class="h-20 lg:h-24"></div>
<div x-data="{ open: false, openDropdown: null, selectedChild: null }" class="fixed z-20 w-full">
    <div x-show="openDropdown !== null || open" class="fixed inset-0 z-30 overflow-y-auto opacity-75 bg-theme-secondary-900" x-cloak @click="openDropdown = null; open = false;"></div>

    <nav class="relative z-30 bg-white shadow-header-smooth">
        <div class="px-8">
            <div class="relative flex justify-between h-20 lg:h-24">

                {{-- LOGO --}}
                <div class="flex items-center flex-shrink-0">
                    <a class="flex items-center" href="{{ route('home') }}">
                        {{ $logo }}
                    </a>
                </div>

                <div class="flex justify-end">
                    <div class="flex items-center justify-end flex-1 sm:items-stretch sm:justify-between">
                        {{-- Desktop Navbar Items --}}
                        <div class="items-center hidden lg:ml-6 lg:flex">
                            {{-- Products Dropdown --}}
                            <a
                                href="#"
                                class="relative inline-flex items-center justify-center h-full px-1 pt-1 mr-8 font-semibold leading-5 transition duration-150 ease-in-out border-b-2 border-transparent text-theme-secondary-700 hover:text-theme-secondary-800 hover:border-theme-secondary-300 focus:outline-none"
                                @click="openDropdown = openDropdown === 'products' ? null : 'products'"
                            >
                                <span :class="{ 'text-theme-primary-600': openDropdown === 'products' }">@lang('menus.documentation.title')</span>
                                <span class="ml-2 transition duration-150 ease-in-out text-theme-primary-600" :class="{ 'rotate-180': openDropdown === 'products' }">@svg('chevron-down', 'h-3 w-3')</span>
                            </a>
                            <div x-show.transition.origin.top="openDropdown === 'products'" class="absolute top-0 z-30 max-w-4xl mt-24 bg-white rounded-b-lg" x-cloak>
                                <div class="flex flex-col w-72 pt-2 pb-6">
                                    @foreach ($productsMenu as $menuItem)
                                        <x-product-link :name="$menuItem['name']" :slug="$menuItem['slug']" :coming-soon="$menuItem['is_coming_soon']">
                                            <x-slot name="customIcon">
                                                @svg('navbar-'.$menuItem['slug'], 'h-8 w-8')
                                            </x-slot>
                                        </x-product-link>
                                    @endforeach
                                    <div class="mx-6 my-2">
                                        <hr class="bg-theme-secondary-300 border-theme-secondary-300" />
                                    </div>
                                    @foreach ($quickAccessMenu as $menuItem)
                                        <x-product-link :name="$menuItem['name']" :slug="$menuItem['slug']" :coming-soon="$menuItem['is_coming_soon']">
                                            <x-slot name="customIcon">
                                                @svg('navbar-'.$menuItem['slug'], 'h-8 w-8')
                                            </x-slot>
                                        </x-product-link>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Rest of the navigation items --}}
                            @foreach ($navigation as $navItem)
                                <a
                                    href="{{ route($navItem['route'], $navItem['params'] ?? []) }}"
                                    class="inline-flex items-center px-1 pt-1 font-semibold leading-5 border-b-2 @if(Route::current()->getName() === $navItem['route']) border-theme-primary-600 text-theme-secondary-900 @else border-transparent text-theme-secondary-700 hover:text-theme-secondary-800 hover:border-theme-secondary-300 @endif focus:outline-none transition duration-150 ease-in-out h-full @if(!$loop->first) ml-8 @endif"
                                    @click="openDropdown = null;"
                                >
                                    {{ $navItem['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:pr-0">
                        {{-- Mobile Hamburger icon --}}
                        <div class="flex items-center lg:hidden sm:ml-4">
                            <button @click="open = !open" class="inline-flex items-center justify-center p-2 transition duration-150 ease-in-out rounded-md text-theme-secondary-900">
                                <span :class="{'hidden': open, 'inline-flex': !open }">@svg('menu-open', 'h-4 w-4')</span>
                                <span :class="{'hidden': !open, 'inline-flex': open }" x-cloak>@svg('menu-close', 'h-4 w-4')</span>
                            </button>
                        </div>
                    </div>

                    {{-- Search --}}
                    {{-- <livewire:navbar-search /> --}}
                </div>
            </div>
        </div>


        {{-- Mobile dropdown --}}
        <div :class="{'block': open, 'hidden': !open}" class="border-t-2 lg:hidden border-theme-secondary-200">
            <div class="py-8 overflow-y-auto sm:py-10 rounded-b-xl" style="max-height: calc(100vh - 10rem)">
                {{-- Products dropdown --}}
                <a
                    href="#"
                    class="flex items-center justify-between w-full px-9 py-3 font-semibold border-l-2 border-transparent text-theme-secondary-900"
                    @click="openDropdown = openDropdown === 'products' ? null : 'products'"
                >
                    <span :class="{ 'text-theme-primary-600': openDropdown === 'products' }">@lang('menus.documentation.title')</span>
                    <span class="ml-2 transition duration-150 ease-in-out text-theme-primary-600" :class="{ 'rotate-180': openDropdown === 'products' }">@svg('chevron-down', 'h-3 w-3')</span>
                </a>
                <div x-show="openDropdown === 'products'" class="space-y-4" x-cloak>
                    <div class="pr-8 ml-9 border-l border-theme-secondary-200">
                        @foreach ($productsMenu as $menuItem)
                            <div class="flex flex-col w-full">
                                <x-product-link :name="$menuItem['name']" :slug="$menuItem['slug']" :coming-soon="$menuItem['is_coming_soon']">
                                    <x-slot name="customIcon">
                                        @svg('navbar-'.$menuItem['slug'], 'h-6 w-6')
                                    </x-slot>
                                </x-product-link>
                            </div>
                        @endforeach
                    </div>
                    <div class="pr-8 ml-9 border-l border-theme-secondary-200">
                        @foreach ($quickAccessMenu as $menuItem)
                            <x-product-link :name="$menuItem['name']" :slug="$menuItem['slug']" :coming-soon="$menuItem['is_coming_soon']">
                                <x-slot name="customIcon">
                                    @svg('navbar-'.$menuItem['slug'], 'h-6 w-6')
                                </x-slot>
                            </x-product-link>
                        @endforeach
                    </div>
                </div>

                {{-- Rest of the navigation items --}}
                @foreach ($navigation as $navItem)
                    <x-ark-sidebar-link :route="$navItem['route']" :name="$navItem['label']" :params="$navItem['params'] ?? []" />
                @endforeach
            </div>
        </div>
    </nav>
</div>
