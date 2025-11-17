import Dropdown from "@/Components/General/Dropdown/Dropdown";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import DropdownItem from "@/Components/General/Dropdown/DropdownItem";
import MoonIcon from "@ui/icons/moon.svg?react";
import SunIcon from "@ui/icons/sun.svg?react";
import MoonStarsIcon from "@ui/icons/moon-stars.svg?react";
import useSettings from "@/Providers/Settings/useSettings";

export default function ThemeManager() {
    const { theme, updateTheme } = useSettings();

    return (
        <DropdownProvider>
            <Dropdown
                wrapperClass="relative"
                dropdownClasses="right-0 min-w-[160px]"
                buttonClass="flex items-center focus:outline-none dropdown-button transition-default justify-center p-2 space-x-1.5 h-8 text-sm font-semibold rounded md:px-3 md:bg-white md:border bg-theme-secondary-200 text-theme-secondary-700 md:hover:text-theme-secondary-700 md:border-theme-secondary-300 md:dark:border-theme-dark-700 md:dark:bg-theme-dark-900 md:dark:text-theme-dark-200 md:hover:text-theme-secondary-900 dark:bg-theme-dark-800 dim:bg-theme-dark-700 dark:hover:bg-theme-dark-700 dark:text-theme-dark-50 hover:bg-theme-secondary-200 dim:bg-theme-dark-900 dim:hover:bg-theme-dark-700 w-8 text-theme-primary-500"
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
