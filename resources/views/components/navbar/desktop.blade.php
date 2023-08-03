<div
    x-data="Navbar.dropdown({
        dark: window.getThemeMode() === 'dark',
        open: false,
        showSettings: false
    })"
    @theme-changed.window="dark = $event.detail.theme === 'dark'"
    x-ref="nav"
    class="hidden relative z-40 bg-white border-b md:flex md:flex-col border-theme-secondary-300 dark:bg-theme-secondary-900 dark:border-theme-secondary-800"
>
    <div
        class="flex justify-between items-center w-full content-container"
    >
        <div class="flex items-center">
            <div class="flex flex-shrink-0 items-center">
                <a class="flex items-center" href="{{ route('home') }}">
                    <x-navbar.logo />
                </a>
            </div>
        </div>

        <div class="flex items-center space-x-3" @click.outside="open = false">
            <div class="flex justify-end">
                <div class="flex flex-1 justify-end items-center sm:justify-between sm:items-stretch">
                    {{-- Desktop Navbar Items --}}
                    <div class="hidden items-center -mx-4 sm:h-16 md:flex h-[3.25rem]">
                        @foreach ($navigation as $navItem)
                            @if (Arr::exists($navItem, 'children'))
                                <div
                                    class="relative h-full"
                                    @click.outside="() => {
                                        if (openDropdown === '{{ $navItem['label'] }}') {
                                            openDropdown = null;
                                        }
                                    }"
                                >
                                    <a
                                        href="javascript:void(0)"
                                        class="inline-flex relative justify-center items-center px-1 pt-px mr-6 h-full font-semibold leading-5 border-b-2 border-transparent transition duration-150 ease-in-out focus:ring-inset focus:outline-none text-theme-secondary-700 dark:text-theme-secondary-400 hover:text-theme-secondary-800"
                                        :class="openDropdown === '{{ $navItem['label'] }}' ? '!border-theme-primary-600' : 'dark:hover:border-theme-secondary-600 hover:border-theme-primary-300'"
                                        @click="openDropdown = openDropdown === '{{ $navItem['label'] }}' ? null : '{{ $navItem['label'] }}'"
                                    >
                                        <span :class="{ 'text-theme-secondary-700 dark:text-theme-secondary-200': openDropdown === '{{ $navItem['label'] }}' }">{{ $navItem['label'] }}</span>
                                        <span class="ml-2 transition duration-150 ease-in-out text-theme-secondary-700 dark:text-theme-secondary-400" :class="{ 'rotate-180': openDropdown === '{{ $navItem['label'] }}' }">
                                            <x-ark-icon name="arrows.chevron-down-small" size="xs" />
                                        </span>
                                    </a>

                                    <div
                                        x-show="openDropdown === '{{ $navItem['label'] }}'"
                                        class="absolute z-30 max-w-4xl whitespace-nowrap bg-white rounded-lg shadow-lg top-[4.5rem] dark:bg-theme-secondary-800"
                                        x-transition.origin.top
                                        x-cloak
                                    >
                                        <div class="flex flex-col pt-2 pb-2">
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
            </div>
        </div>
    </div>

    {{-- <div class="absolute bottom-0 -mb-px w-full border-b border-theme-secondary-300 dark:border-theme-secondary-800"></div> --}}
</div>
