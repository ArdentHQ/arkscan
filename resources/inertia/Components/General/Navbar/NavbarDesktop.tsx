import { Navigation } from "@/types";
import NavbarLogo from "./NavbarLogo";
import useShareData from "@/hooks/use-shared-data";
import classNames from "classnames";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import Dropdown from "@/Components/General/Dropdown/Dropdown";
import DropdownItem from "@/Components/General/Dropdown/DropdownItem";
import ChevronDownSmallIcon from "@ui/icons/arrows/chevron-down-small.svg?react";

const NavbarItem = ({ routeName, label }: { routeName: string; label: string }) => {
    const { currentRoute } = useShareData();

    const link = route(routeName);
    const isActive = currentRoute === link;
    return (
        <a
            href={link}
            className={classNames({
                "group relative mx-4 inline-flex h-full rounded border-t-2 border-transparent px-2 font-semibold leading-5 transition duration-150 ease-in-out focus:outline-none focus:ring-inset": true,
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
        </a>
    );
};

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

                <div className="flex items-center space-x-3">
                    <div className="flex justify-end">
                        <div className="flex flex-1 items-center justify-end sm:items-stretch sm:justify-between">
                            <div className="-mx-4 hidden h-[3.25rem] items-center sm:h-16 md:flex">
                                {navigation.map((navItem) => (
                                    <>
                                        {navItem.children ? (
                                            <DropdownProvider>
                                                <Dropdown
                                                    wrapperClass="relative h-full"
                                                    useDefaultButtonClasses={false}
                                                    buttonClass="inline-flex relative justify-center items-center px-1 pt-px mr-6 h-full font-semibold leading-5 border-b-2 border-transparent transition duration-150 ease-in-out focus:ring-inset focus:outline-none text-theme-secondary-700 dark:text-theme-dark-200 hover:text-theme-secondary-800 hover:dark:text-theme-dark-50"
                                                    dropdownClasses="w-auto"
                                                    placement="bottom-start"
                                                    button={({ isOpen }) => (
                                                        <>
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
                                                        </>
                                                    )}
                                                >
                                                    {navItem.children.map((child) => (
                                                        <DropdownItem asChild key={child.label}>
                                                            <a
                                                                href={child.url ?? route(child.route!)}
                                                                target={child.url ? "_blank" : "_self"}
                                                            >
                                                                {child.label}
                                                            </a>
                                                        </DropdownItem>
                                                    ))}
                                                </Dropdown>
                                            </DropdownProvider>
                                        ) : (
                                            <NavbarItem routeName={navItem.route!} label={navItem.label} />
                                        )}
                                    </>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
