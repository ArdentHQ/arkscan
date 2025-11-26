import { Navigation } from "@/types";
import NavbarLogo from "./NavbarLogo";
import classNames from "classnames";
import { useTranslation } from "react-i18next";
import MagnifyingGlassSmallIcon from "@ui/icons/magnifying-glass-small.svg?react";
import MenuIcon from "@ui/icons/menu.svg?react";
import MenuShowIcon from "@ui/icons/menu-show.svg?react";
import { useState } from "react";
import useShareData from "@/hooks/use-shared-data";

const NavbarMobileButton = ({ className, disabled, ...props }: React.ButtonHTMLAttributes<HTMLButtonElement>) => {
    return (
        <button
            type="button"
            className={classNames(
                "transition-default mx-1 flex items-center justify-center rounded p-2.5 focus:outline-none focus:ring-inset md:mx-4",
                {
                    "text-theme-secondary-400": disabled,
                    "text-theme-secondary-600 hover:bg-theme-primary-100 hover:text-theme-primary-700 dark:text-theme-dark-300 dark:hover:bg-theme-secondary-800 dark:hover:text-theme-secondary-100":
                        !disabled,
                },
                className,
            )}
            disabled={disabled}
            {...props}
        />
    );
};

export default function NavbarDesktop({ navigation }: { navigation: Navigation }) {
    const { t } = useTranslation();
    const [open, setOpen] = useState(false);
    const { isDownForMaintenance } = useShareData();
    return (
        <header className="flex flex-col md:hidden">
            <div
                className="fixed z-20 w-full md:relative"
                // x-data="Navbar.dropdown({
                //     theme: window.getThemeMode(),
                //     open: false,
                //     showSettings: false,

                //     lockBody() {
                //         return ! this.isIOSSafari() && window.innerWidth <= this.lockBodyBreakpoint;
                //     },

                //     isIOSSafari() {
                //         if (! /(Macintosh)|(Mac OS)|(iPad)|(iPod)|(iPhone)/.test(window.navigator.userAgent)) {
                //             return false;
                //         }

                //         if (! /(Safari)/.test(window.navigator.userAgent)) {
                //             return false;
                //         }

                //         if (! /(AppleWebKit)/.test(window.navigator.userAgent)) {
                //             return false;
                //         }

                //         return ('ontouchstart' in window) || (navigator.MaxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0);
                //     },
                // })"
                // @theme-changed.window="theme = $event.detail.theme"
            >
                {/* <div
            x-show="openDropdown !== null || open"
            className="overflow-y-auto fixed inset-0 z-30 md:relative"
            // @click="openDropdown = null; open = false;"
            x-cloak
        ></div> */}

                <nav
                    className="relative z-30 border-b border-theme-secondary-300 bg-white dark:border-theme-dark-800 dark:bg-theme-dark-900"
                    // @click.outside="open = false"
                >
                    <div className="content-container relative flex h-[3.25rem] w-full justify-between sm:h-16">
                        <div className="flex flex-shrink-0 items-center">
                            <a className="flex items-center" href="{{ route('home') }}">
                                <NavbarLogo />
                            </a>
                        </div>

                        <div className="flex justify-end">
                            <div className="flex justify-end">
                                <div className="flex items-center space-x-1">
                                    <NavbarMobileButton
                                        disabled={isDownForMaintenance}
                                        onClick={() => {
                                            console.log("search");
                                        }}
                                    >
                                        <MagnifyingGlassSmallIcon className="h-4 w-4" />

                                        <span className="sr-only">{t("actions.search")}</span>
                                    </NavbarMobileButton>

                                    <NavbarMobileButton onClick={() => setOpen(!open)}>
                                        <span
                                            className={classNames({
                                                hidden: open,
                                                "inline-flex": !open,
                                            })}
                                        >
                                            <MenuIcon className="h-4 w-4" />
                                        </span>

                                        <span
                                            className={classNames({
                                                hidden: !open,
                                                "inline-flex": open,
                                            })}
                                        >
                                            <MenuShowIcon className="h-4 w-4" />
                                        </span>
                                    </NavbarMobileButton>
                                    {/* <x-navbar.mobile.button
                                @click="Livewire.dispatch('openSearchModal')"
                                dusk="navigation-search-modal-trigger"
                                :disabled="app()->isDownForMaintenance()"
                            >
                                <x-ark-icon name="magnifying-glass-small" />

                                <span className="sr-only">
                                    @lang('actions.search')
                                </span>
                            </x-navbar.mobile.button>

                            <x-navbar.mobile.button @click="toggle">
                                <span :className="{'hidden': open, 'inline-flex': !open }">
                                    <x-ark-icon name="menu" />
                                </span>

                                <span :className="{'hidden': !open, 'inline-flex': open }" x-cloak>
                                    <x-ark-icon name="menu-show" />
                                </span>
                            </x-navbar.mobile.button> */}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        aria-hidden={!open}
                        className={classNames("transition-all duration-200 ease-in-out", {
                            "pointer-events-none max-h-0 overflow-hidden opacity-0": !open,
                            "max-h-screen opacity-100": open,
                        })}
                    >
                        {/* 
                <div className="border-t-2 shadow-xl border-theme-secondary-200 dim:border-theme-dark-700 dark:border-theme-dark-800">
                    <div className="pt-2 bg-white rounded-b-lg dark:bg-theme-dark-700">
                        @foreach ($navigation as $navItem)
                            @if (Arr::exists($navItem, 'children'))
                                <div className="relative h-full dark:bg-theme-dark-700">
                                    <a
                                        href="#"
                                        className="inline-flex relative justify-between items-center py-3 px-6 w-full h-full font-semibold leading-5 focus:ring-inset focus:outline-none text-theme-secondary-700 dark:text-theme-dark-50 hover:text-theme-secondary-800"
                                        @click="openDropdown = openDropdown === '{{ $navItem['label'] }}' ? null : '{{ $navItem['label'] }}'"
                                    >
                                        <span :className="{ 'text-theme-secondary-700 dark:text-theme-dark-50': openDropdown === '{{ $navItem['label'] }}' }">{{ $navItem['label'] }}</span>
                                        <span className="ml-2 text-theme-secondary-700 dark:text-theme-dark-50" :className="{ 'rotate-180': openDropdown === '{{ $navItem['label'] }}' }">
                                            <x-ark-icon name="arrows.chevron-down-small" size="xs" />
                                        </span>
                                    </a>

                                    <div
                                        x-show="openDropdown === '{{ $navItem['label'] }}'"
                                        className="bg-white dark:bg-theme-dark-700"
                                        x-cloak
                                    >
                                        <div className="flex flex-col pt-2 pb-2 w-full">
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

                        <div className="py-4 dark:bg-black bg-theme-secondary-100">
                            <div className="mx-6 space-y-3 divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-dark-800">
                                <x-navbar.mobile.setting-item title="{{ trans('general.select_theme') }}">
                                    <livewire:navbar.mobile-dark-mode-toggle
                                        setting="theme"
                                        :options="[
                                            [
                                                'icon' => 'sun',
                                                'value' => 'light',
                                            ],
                                            [
                                                'icon' => 'moon',
                                                'value' => 'dark',
                                            ],
                                            [
                                                'icon' => 'moon-stars',
                                                'value' => 'dim',
                                            ],
                                        ]"
                                    />
                                </x-navbar.mobile.setting-item>

                                <x-navbar.mobile.setting-item
                                    title="{{ trans('general.select_network') }}"
                                    className="pt-3"
                                >
                                    <x-navbar.network-dropdown />
                                </x-navbar.mobile.setting-item>

                                <div className="flex pt-3 font-semibold dark:text-theme-dark-500">
                                    <livewire:navbar.price-ticker />
                                </div>
                            </div>
                        </div>

                        @if (config('arkscan.arkconnect.enabled', false))
                            <x-navbar.arkconnect />
                        @endif
                    </div>
                </div>
                        */}
                    </div>
                </nav>
            </div>
        </header>
    );
}
