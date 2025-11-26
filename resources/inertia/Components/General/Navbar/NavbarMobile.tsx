import { Navigation } from "@/types";
import NavbarLogo from "./NavbarLogo";
import classNames from "classnames";
import { useTranslation } from "react-i18next";
import MagnifyingGlassSmallIcon from "@ui/icons/magnifying-glass-small.svg?react";
import MenuIcon from "@ui/icons/menu.svg?react";
import MenuShowIcon from "@ui/icons/menu-show.svg?react";
import { useState } from "react";
import useShareData from "@/hooks/use-shared-data";
import ChevronDownSmallIcon from "@ui/icons/arrows/chevron-down-small.svg?react";
import PriceTicker from "@/Components/General/PriceTicker/PriceTicker";
import NetworkDropdown from "@/Components/General/NetworkDropdown/NetworkDropdown";
import NavbarMobileThemeToggle from "./NavbarMobileThemeToggle";

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

const NavbarMobileItem = ({
    className,
    routeName,
    url,
    children,
    ...props
}: React.AnchorHTMLAttributes<HTMLAnchorElement> & { routeName?: string; url?: string }) => {
    const { currentRoute } = useShareData();

    const link = routeName ? route(routeName) : url;
    const isActive = currentRoute === link;

    return (
        <a
            href={link}
            target={routeName ? "_self" : "_blank"}
            className={classNames(
                "group relative inline-flex h-full w-full py-3 font-semibold leading-5 transition duration-150 ease-in-out focus:outline-none",
                {
                    "w-full border-l-4 border-theme-primary-600 bg-theme-primary-50 text-theme-secondary-900 dark:border-theme-dark-blue-500 dark:bg-theme-dark-950 dark:text-theme-dark-50":
                        isActive,
                    "hover:background-theme-secondary-200 text-theme-secondary-700 hover:text-theme-secondary-800 dark:text-theme-dark-50 dark:hover:text-theme-secondary-400":
                        !isActive,
                },
                className,
            )}
            {...props}
        >
            <span
                className={classNames("flex h-full w-full items-center", {
                    "pl-5": isActive,
                    "pl-6": !isActive,
                })}
            >
                <span>{children}</span>
            </span>
        </a>
    );
};
const NavbarMobileListItem = ({
    className,
    routeName,
    url,
    children,
    ...props
}: React.AnchorHTMLAttributes<HTMLAnchorElement> & { routeName?: string; url?: string }) => {
    const { currentRoute } = useShareData();

    const link = routeName ? route(routeName) : url;
    const isActive = currentRoute === link;

    return (
        <a
            href={link}
            target={route ? "_self" : "_blank"}
            className={classNames(
                "transition-default group relative ml-6 inline-flex h-full w-full border-l border-theme-secondary-300 px-6 py-3 font-semibold leading-5 hover:bg-theme-secondary-200 focus:outline-none dark:border-theme-dark-500 dark:hover:bg-theme-dark-900",
                {
                    "text-theme-secondary-900 dark:text-theme-dark-50": isActive,
                    "text-theme-secondary-700 hover:text-theme-secondary-800 dark:text-theme-dark-50 dark:hover:text-theme-secondary-50":
                        !isActive,
                },
                className,
            )}
            {...props}
        >
            <span className="transition-default flex h-full w-full items-center text-theme-secondary-700 group-hover:text-theme-secondary-900 dark:text-theme-dark-50 dark:group-hover:text-white">
                <span>{children}</span>
            </span>
        </a>
    );
};

const SettingsItem = ({
    title,
    className,
    children,
    ...props
}: React.HTMLAttributes<HTMLDivElement> & { title: string }) => {
    return (
        <div className={classNames("flex items-center justify-between", className)} {...props}>
            <div className="font-semibold dark:text-theme-dark-200">{title}</div>

            <div>{children}</div>
        </div>
    );
};

export default function NavbarDesktop({ navigation }: { navigation: Navigation }) {
    const { t } = useTranslation();
    const [open, setOpen] = useState(false);
    const [openDropdown, setOpenDropdown] = useState<string | null>(null);
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
                {openDropdown !== null ||
                    (open && (
                        <div
                            className="fixed inset-0 z-30 overflow-y-auto md:relative"
                            onClick={() => {
                                setOpenDropdown(null);

                                setOpen(false);
                            }}
                        ></div>
                    ))}

                <nav className="relative z-30 border-b border-theme-secondary-300 bg-white dark:border-theme-dark-800 dark:bg-theme-dark-900">
                    <div className="content-container relative flex h-[3.25rem] w-full justify-between sm:h-16">
                        <div className="flex flex-shrink-0 items-center">
                            <a className="flex items-center" href={route("home")}>
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
                                        <MagnifyingGlassSmallIcon className="h-5 w-5" />

                                        <span className="sr-only">{t("actions.search")}</span>
                                    </NavbarMobileButton>

                                    <NavbarMobileButton onClick={() => setOpen(!open)}>
                                        <span
                                            className={classNames({
                                                hidden: open,
                                                "inline-flex": !open,
                                            })}
                                        >
                                            <MenuIcon className="h-5 w-5" />
                                        </span>

                                        <span
                                            className={classNames({
                                                hidden: !open,
                                                "inline-flex": open,
                                            })}
                                        >
                                            <MenuShowIcon className="h-5 w-5" />
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
                        <div className="border-t-2 border-theme-secondary-200 shadow-xl dim:border-theme-dark-700 dark:border-theme-dark-800">
                            <div className="rounded-b-lg bg-white pt-2 dark:bg-theme-dark-700">
                                {navigation.map((navItem, index) => (
                                    <div key={index} className="relative h-full">
                                        {navItem.children ? (
                                            <div className="relative h-full dark:bg-theme-dark-700">
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        setOpenDropdown(
                                                            openDropdown === navItem.label ? null : navItem.label,
                                                        )
                                                    }
                                                    className="relative inline-flex h-full w-full items-center justify-between px-6 py-3 font-semibold leading-5 text-theme-secondary-700 hover:text-theme-secondary-800 focus:outline-none focus:ring-inset dark:text-theme-dark-50"
                                                >
                                                    <span
                                                        className={classNames({
                                                            "text-theme-secondary-700 dark:text-theme-dark-50":
                                                                openDropdown === navItem.label,
                                                        })}
                                                    >
                                                        {navItem.label}
                                                    </span>

                                                    <span
                                                        className={classNames(
                                                            "ml-2 text-theme-secondary-700 dark:text-theme-dark-50",
                                                            {
                                                                "rotate-180": openDropdown === navItem.label,
                                                            },
                                                        )}
                                                    >
                                                        <ChevronDownSmallIcon className="h-3 w-3" />
                                                    </span>
                                                </button>

                                                {openDropdown === navItem.label && (
                                                    <div className="bg-white dark:bg-theme-dark-700">
                                                        <div className="flex w-full flex-col pb-2 pt-2">
                                                            {navItem.children?.map((child, index) => (
                                                                <NavbarMobileListItem
                                                                    key={index}
                                                                    routeName={child.route}
                                                                    url={child.url}
                                                                >
                                                                    {child.label}
                                                                </NavbarMobileListItem>
                                                            ))}
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        ) : (
                                            <NavbarMobileItem routeName={navItem.route} url={navItem.url}>
                                                {navItem.label}
                                            </NavbarMobileItem>
                                        )}
                                    </div>
                                ))}

                                <div className="bg-theme-secondary-100 py-5 dark:bg-black">
                                    <div className="mx-6 space-y-3 divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-dark-800">
                                        <SettingsItem title={t("general.select_network")} className="pt-3">
                                            <NavbarMobileThemeToggle />
                                        </SettingsItem>

                                        <SettingsItem title={t("general.select_network")} className="pt-3">
                                            <NetworkDropdown />
                                        </SettingsItem>

                                        <div className="flex pt-3 font-semibold dark:text-theme-dark-500">
                                            <PriceTicker />
                                        </div>
                                    </div>
                                </div>

                                {/* 
                      

                        

                        @if (config('arkscan.arkconnect.enabled', false))
                            <x-navbar.arkconnect />
                        @endif
                        */}
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </header>
    );
}
