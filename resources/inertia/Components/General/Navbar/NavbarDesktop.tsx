import { Navigation } from "@/types";
import NavbarLogo from "./NavbarLogo";
import useShareData from "@/hooks/use-shared-data";
import classNames from "classnames";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import Dropdown from "@/Components/General/Dropdown/Dropdown";
import DropdownItem from "@/Components/General/Dropdown/DropdownItem";
import ChevronDownSmallIcon from "@ui/icons/arrows/chevron-down-small.svg?react";
import { Link } from "@inertiajs/react";

const NavbarItem = ({ routeName, label }: { routeName: string; label: string }) => {
    const { currentRoute } = useShareData();

    const link = route(routeName);
    const isActive = currentRoute === routeName;

    return (
        <Link
            href={link}
            className={classNames({
                "group relative mx-4 inline-flex h-full rounded px-2 pt-[2px] font-semibold leading-5 transition duration-150 ease-in-out focus:outline-none focus-visible:ring-inset": true,
                "text-theme-secondary-900 dark:text-theme-dark-50": isActive,
                "text-theme-secondary-700 hover:text-theme-secondary-800 dark:text-theme-dark-200 dark:hover:text-theme-secondary-400":
                    !isActive,
            })}
        >
            <span
                className={classNames({
                    "flex h-full w-full items-center border-b-2": true,
                    "border-theme-primary-600": isActive,
                    "border-transparent group-hover:border-theme-primary-300 group-hover:dark:border-theme-dark-600":
                        !isActive,
                })}
            >
                <span>{label}</span>
            </span>
        </Link>
    );
};

export default function NavbarDesktop({ navigation }: { navigation: Navigation }) {
    return (
        <div className="relative z-30 hidden border-b border-theme-secondary-300 bg-white dark:border-theme-dark-700 dark:bg-theme-dark-900 md:flex md:flex-col">
            <div className="content-container flex w-full items-center justify-between">
                <div className="flex items-center">
                    <div className="flex flex-shrink-0 items-center">
                        <Link className="flex items-center" href={route("home")}>
                            <NavbarLogo />
                        </Link>
                    </div>
                </div>

                <div className="flex items-center space-x-3">
                    <div className="flex justify-end">
                        <div className="flex flex-1 items-center justify-end sm:items-stretch sm:justify-between mr-1">
                            <div className="-mx-4 hidden h-[3.25rem] items-center sm:h-16 md:flex">
                                {navigation.map((navItem, index) => (
                                    <div key={index} className="relative h-full">
                                        {navItem.children ? (
                                            <DropdownProvider>
                                                <Dropdown
                                                    wrapperClass="relative h-full mr-3"
                                                    useDefaultButtonClasses={false}
                                                    buttonClass="inline-flex h-full px-2 focus-visible:ring-inset"
                                                    dropdownClasses="w-auto"
                                                    placement="bottom-start"
                                                    button={({ isOpen }) => (
                                                        <div
                                                            className={classNames(
                                                                "relative inline-flex h-full items-center justify-center border-b-2 border-transparent pt-px font-semibold leading-5 text-theme-secondary-700 transition duration-150 ease-in-out hover:border-theme-primary-300 focus:outline-none focus:ring-inset dark:text-theme-dark-200 hover:dark:text-theme-dark-50",
                                                                {
                                                                    "!border-theme-primary-600": isOpen,
                                                                    "hover:border-theme-primary-300 dark:hover:border-theme-secondary-600":
                                                                        !isOpen,
                                                                },
                                                            )}
                                                        >
                                                            <span
                                                                className={classNames({
                                                                    "text-theme-secondary-700 dark:text-theme-dark-50":
                                                                        isOpen,
                                                                })}
                                                            >
                                                                {navItem.label}
                                                            </span>

                                                            <span
                                                                className={classNames(
                                                                    "ml-2 text-theme-secondary-700 transition duration-150 ease-in-out dark:text-theme-dark-200",
                                                                    {
                                                                        "rotate-180 dark:text-theme-dark-50": isOpen,
                                                                    },
                                                                )}
                                                            >
                                                                <ChevronDownSmallIcon className="h-3 w-3" />
                                                            </span>
                                                        </div>
                                                    )}
                                                >
                                                    {navItem.children.map((child, index) => (
                                                        <DropdownItem asChild key={child.url ?? child.route ?? index}>
                                                            {child.url ? (
                                                                <a href={child.url} target="_blank">
                                                                    {child.label}
                                                                </a>
                                                            ) : (
                                                                <Link href={route(child.route!)}>{child.label}</Link>
                                                            )}
                                                        </DropdownItem>
                                                    ))}
                                                </Dropdown>
                                            </DropdownProvider>
                                        ) : (
                                            <NavbarItem routeName={navItem.route!} label={navItem.label} />
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
