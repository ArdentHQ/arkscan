import { disableBodyScroll, enableBodyScroll } from "body-scroll-lock";
import { Navigation } from "@/types";
import NavbarLogo from "./NavbarLogo";
import classNames from "classnames";
import { useTranslation } from "react-i18next";
import MagnifyingGlassSmallIcon from "@ui/icons/magnifying-glass-small.svg?react";
import MenuIcon from "@ui/icons/menu.svg?react";
import MenuShowIcon from "@ui/icons/menu-show.svg?react";
import { useEffect, useMemo, useRef, useState } from "react";
import useShareData from "@/hooks/use-shared-data";
import ChevronDownSmallIcon from "@ui/icons/arrows/chevron-down-small.svg?react";
import PriceTicker from "@/Components/General/PriceTicker/PriceTicker";
import NetworkDropdown from "@/Components/General/NetworkDropdown/NetworkDropdown";
import NavbarMobileThemeToggle from "./NavbarMobileThemeToggle";
import NavbarArkConnect from "../NavbarArkConnect/NavbarArkConnect";
import { useNavbar } from "./NavbarContext";
import { NavbarResultsMobile } from "@/Components/General/NavbarSearch/NavbarResults";
import { isIosSafari } from "@/utils/is-ios-safari";
import { Link } from "@inertiajs/react";

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
}: Omit<React.AnchorHTMLAttributes<HTMLAnchorElement>, "href"> & { routeName?: string; url?: string }) => {
    const { currentRoute } = useShareData();

    const link = routeName ? route(routeName) : (url ?? "#");
    const isActive = currentRoute === link;

    if (routeName) {
        return (
            <Link
                href={link}
                className={classNames(
                    "group relative inline-flex h-full w-full py-3 leading-5 font-semibold transition duration-150 ease-in-out focus:outline-none",
                    {
                        "border-theme-primary-600 bg-theme-primary-50 text-theme-secondary-900 dark:border-theme-dark-blue-500 dark:bg-theme-dark-950 dark:text-theme-dark-50 w-full border-l-4":
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
            </Link>
        );
    }

    return (
        <a
            href={link}
            target="_blank"
            className={classNames(
                "group relative inline-flex h-full w-full py-3 leading-5 font-semibold transition duration-150 ease-in-out focus:outline-none",
                {
                    "border-theme-primary-600 bg-theme-primary-50 text-theme-secondary-900 dark:border-theme-dark-blue-500 dark:bg-theme-dark-950 dark:text-theme-dark-50 w-full border-l-4":
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
}: Omit<React.AnchorHTMLAttributes<HTMLAnchorElement>, "href"> & { routeName?: string; url?: string }) => {
    const { currentRoute } = useShareData();

    const link = routeName ? route(routeName) : (url ?? "#");
    const isActive = currentRoute === link;

    if (routeName) {
        return (
            <Link
                href={link}
                className={classNames(
                    "transition-default group border-theme-secondary-300 hover:bg-theme-secondary-200 dark:border-theme-dark-500 dark:hover:bg-theme-dark-900 relative ml-6 inline-flex h-full w-full border-l px-6 py-3 leading-5 font-semibold focus:outline-none",
                    {
                        "text-theme-secondary-900 dark:text-theme-dark-50": isActive,
                        "text-theme-secondary-700 hover:text-theme-secondary-800 dark:text-theme-dark-50 dark:hover:text-theme-secondary-50":
                            !isActive,
                    },
                    className,
                )}
                {...props}
            >
                <span className="transition-default text-theme-secondary-700 group-hover:text-theme-secondary-900 dark:text-theme-dark-50 flex h-full w-full items-center dark:group-hover:text-white">
                    <span>{children}</span>
                </span>
            </Link>
        );
    }

    return (
        <a
            href={link}
            target={route ? "_self" : "_blank"}
            className={classNames(
                "transition-default group border-theme-secondary-300 hover:bg-theme-secondary-200 dark:border-theme-dark-500 dark:hover:bg-theme-dark-900 relative ml-6 inline-flex h-full w-full border-l px-6 py-3 leading-5 font-semibold focus:outline-none",
                {
                    "text-theme-secondary-900 dark:text-theme-dark-50": isActive,
                    "text-theme-secondary-700 hover:text-theme-secondary-800 dark:text-theme-dark-50 dark:hover:text-theme-secondary-50":
                        !isActive,
                },
                className,
            )}
            {...props}
        >
            <span className="transition-default text-theme-secondary-700 group-hover:text-theme-secondary-900 dark:text-theme-dark-50 flex h-full w-full items-center dark:group-hover:text-white">
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
            <div className="dark:text-theme-dark-200 font-semibold">{title}</div>

            <div>{children}</div>
        </div>
    );
};

export default function NavbarMobile({ navigation }: { navigation: Navigation }) {
    const { t } = useTranslation();
    const [open, setOpen] = useState(false);
    const [openDropdown, setOpenDropdown] = useState<string | null>(null);
    const { isDownForMaintenance, arkconnectConfig } = useShareData();
    const { setSearchModalOpen } = useNavbar();
    const navbarRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const shouldLockBody = !isIosSafari() && window.innerWidth <= 640;

        if (open) {
            if (shouldLockBody) {
                disableBodyScroll(navbarRef.current);
            }
        } else {
            enableBodyScroll(navbarRef.current);
        }
    }, [open]);

    return (
        <header className="flex flex-col md:hidden">
            <div
                className="fixed z-20 w-full md:relative"
                // @TODO: How is the logic of `lockBody` and `isIOSSafari`
                // relevant that you can find in the file
                // `resources/views/components/navbar/mobile.blade.php`?
                // there is also a `@theme-changed.window="theme = $event.detail.theme"
                // https://app.clickup.com/t/86dyk997a
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

                <nav
                    ref={navbarRef}
                    className="border-theme-secondary-300 dark:border-theme-dark-800 dark:bg-theme-dark-900 relative z-30 border-b bg-white"
                >
                    <div className="content-container relative flex h-[3.25rem] w-full justify-between sm:h-16">
                        <div className="flex flex-shrink-0 items-center">
                            <Link className="flex items-center" href={route("home")}>
                                <NavbarLogo />
                            </Link>
                        </div>

                        <div className="flex justify-end">
                            <div className="flex justify-end">
                                <div className="flex items-center space-x-1">
                                    <NavbarMobileButton
                                        disabled={isDownForMaintenance}
                                        onClick={() => {
                                            setSearchModalOpen(true);
                                        }}
                                    >
                                        <MagnifyingGlassSmallIcon className="h-5 w-5" />

                                        <span className="sr-only">{t("actions.search")}</span>
                                    </NavbarMobileButton>

                                    <NavbarMobileButton onClick={() => setOpen(!open)} aria-label={t("actions.menu")}>
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
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        aria-hidden={!open}
                        inert={!open ? true : undefined}
                        className={classNames("transition-all duration-200 ease-in-out", {
                            "pointer-events-none max-h-0 overflow-hidden opacity-0": !open,
                            "max-h-screen opacity-100": open,
                        })}
                    >
                        <div className="border-theme-secondary-200 dim:border-theme-dark-700 dark:border-theme-dark-800 border-t-2 shadow-xl">
                            <div className="dark:bg-theme-dark-700 rounded-b-lg bg-white pt-2">
                                {navigation.map((navItem, index) => (
                                    <div key={index} className="relative h-full">
                                        {navItem.children ? (
                                            <div className="dark:bg-theme-dark-700 relative h-full">
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        setOpenDropdown(
                                                            openDropdown === navItem.label ? null : navItem.label,
                                                        )
                                                    }
                                                    className="text-theme-secondary-700 hover:text-theme-secondary-800 dark:text-theme-dark-50 relative inline-flex h-full w-full items-center justify-between px-6 py-3 leading-5 font-semibold focus:outline-none focus:ring-inset"
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
                                                            "text-theme-secondary-700 dark:text-theme-dark-50 ml-2",
                                                            {
                                                                "rotate-180": openDropdown === navItem.label,
                                                            },
                                                        )}
                                                    >
                                                        <ChevronDownSmallIcon className="h-3 w-3" />
                                                    </span>
                                                </button>

                                                {openDropdown === navItem.label && (
                                                    <div className="dark:bg-theme-dark-700 bg-white">
                                                        <div className="flex w-full flex-col pt-2 pb-2">
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
                                    <div className="divide-theme-secondary-300 dark:divide-theme-dark-800 mx-6 space-y-3 divide-y divide-dashed">
                                        <SettingsItem title={t("general.select_network")}>
                                            <NavbarMobileThemeToggle />
                                        </SettingsItem>

                                        <SettingsItem title={t("general.select_network")} className="pt-3">
                                            <NetworkDropdown />
                                        </SettingsItem>

                                        <div className="dark:text-theme-dark-500 flex pt-3 font-semibold">
                                            <PriceTicker />
                                        </div>
                                    </div>
                                </div>

                                {arkconnectConfig.enabled && <NavbarArkConnect />}
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <NavbarResultsMobile />
        </header>
    );
}
