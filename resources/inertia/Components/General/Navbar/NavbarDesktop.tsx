import { Navigation } from "@/types";
import NavbarLogo from "./NavbarLogo";

export default function NavbarDesktop({ navigation }: { navigation: Navigation }) {
    return (
        <div className="relative z-30 hidden border-b border-theme-secondary-300 bg-white dark:border-theme-dark-700 dark:bg-theme-dark-900 md:flex md:flex-col">
            <div className="content-container flex w-full items-center justify-between">
                <div className="flex items-center">
                    <div className="flex flex-shrink-0 items-center">
                        <a className="flex items-center" href={route("home")}>
                            <NavbarLogo />
                        </a>
                    </div>
                </div>

                {/* <div className="flex items-center space-x-3" @click.outside="open = false">
            <div className="flex justify-end">
                <div className="flex flex-1 justify-end items-center sm:justify-between sm:items-stretch">
                    <div className="hidden items-center -mx-4 sm:h-16 md:flex h-[3.25rem]">
                        @foreach ($navigation as $navItem)
                            @if (Arr::exists($navItem, 'children'))
                                <div
                                    className="relative h-full"
                                    @click.outside="() => {
                                        if (openDropdown === '{{ $navItem['label'] }}') {
                                            openDropdown = null;
                                        }
                                    }"
                                >
                                    <a
                                        href="javascript:void(0)"
                                        className="inline-flex relative justify-center items-center px-1 pt-px mr-6 h-full font-semibold leading-5 border-b-2 border-transparent transition duration-150 ease-in-out focus:ring-inset focus:outline-none text-theme-secondary-700 dark:text-theme-dark-200 hover:text-theme-secondary-800 hover:dark:text-theme-dark-50"
                                        :className="openDropdown === '{{ $navItem['label'] }}' ? '!border-theme-primary-600' : 'dark:hover:border-theme-secondary-600 hover:border-theme-primary-300'"
                                        @click="openDropdown = openDropdown === '{{ $navItem['label'] }}' ? null : '{{ $navItem['label'] }}'"
                                    >
                                        <span :className="{ 'text-theme-secondary-700 dark:text-theme-dark-50': openDropdown === '{{ $navItem['label'] }}' }">{{ $navItem['label'] }}</span>
                                        <span className="ml-2 transition duration-150 ease-in-out text-theme-secondary-700 dark:text-theme-dark-200" :className="{ 'rotate-180 dark:text-theme-dark-50': openDropdown === '{{ $navItem['label'] }}' }">
                                            <x-ark-icon name="arrows.chevron-down-small" size="xs" />
                                        </span>
                                    </a>

                                    <div
                                        x-show="openDropdown === '{{ $navItem['label'] }}'"
                                        className="absolute z-30 px-1 max-w-4xl whitespace-nowrap bg-white rounded-xl border border-white shadow-lg top-[4.5rem] dark:bg-theme-dark-900 dark:border-theme-dark-700"
                                        x-transition.origin.top
                                        x-cloak
                                    >
                                        <div className="flex flex-col py-[0.125rem]">
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
        </div> */}
            </div>
        </div>
    );
}
