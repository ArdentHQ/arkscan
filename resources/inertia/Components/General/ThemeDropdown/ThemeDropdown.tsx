import Dropdown from "@/Components/General/Dropdown/Dropdown";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import DropdownItem from "@/Components/General/Dropdown/DropdownItem";
import MoonIcon from "@ui/icons/moon.svg?react";
import SunIcon from "@ui/icons/sun.svg?react";
import MoonStarsIcon from "@ui/icons/moon-stars.svg?react";
import useSettings from "@/Providers/Settings/useSettings";
import classNames from "classnames";

export default function ThemeDropdown() {
    const { theme, updateTheme } = useSettings();

    return (
        <DropdownProvider>
            <Dropdown
                wrapperClass="relative"
                dropdownClasses="right-0 min-w-[160px]"
                useDefaultButtonClasses={false}
                buttonClass={classNames(
                    "dropdown-button",
                    "flex items-center justify-center h-8 w-8 px-3 py-2 space-x-1.5",
                    "text-sm font-semibold",
                    "rounded border border-theme-secondary-300",
                    "bg-white hover:text-theme-secondary-700",
                    "dark:bg-theme-dark-900 dark:border-theme-dark-700 dark:text-theme-dark-200",
                    "dim:bg-theme-dark-900 dim:hover:bg-theme-dark-700",
                    "transition-default focus:outline-none",
                    "dim:hover:bg-theme-dark-700 hover:text-theme-secondary-900 dark:hover:bg-theme-dark-700 hover:bg-theme-secondary-200",
                )}
                button={() => (
                    <div className="dim:text-theme-dark-300">
                        {theme === "dark" && <MoonIcon className="h-4 w-4" />}
                        {theme === "light" && <SunIcon className="h-4 w-4" />}
                        {theme === "dim" && <MoonStarsIcon className="h-4 w-4" />}
                    </div>
                )}
            >
                <DropdownItem
                    selected={theme === "light"}
                    onClick={() => {
                        updateTheme("light");
                    }}
                >
                    Light
                </DropdownItem>
                <DropdownItem
                    selected={theme === "dark"}
                    onClick={() => {
                        updateTheme("dark");
                    }}
                >
                    Dark
                </DropdownItem>
                <DropdownItem
                    selected={theme === "dim"}
                    onClick={() => {
                        updateTheme("dim");
                    }}
                >
                    Dim
                </DropdownItem>
            </Dropdown>
        </DropdownProvider>
    );
}
