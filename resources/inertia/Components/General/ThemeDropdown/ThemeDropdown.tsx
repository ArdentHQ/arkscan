import { useEffect } from "react";
import Dropdown from "@/Components/General/Dropdown/Dropdown";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import DropdownItem from "@/Components/General/Dropdown/DropdownItem";
import MoonIcon from "@ui/icons/moon.svg?react";
import SunIcon from "@ui/icons/sun.svg?react";
import MoonStarsIcon from "@ui/icons/moon-stars.svg?react";
import useSettings from "@/Providers/Settings/useSettings";

export default function ThemeDropdown() {
    const { theme, updateTheme } = useSettings();

    return (
        <DropdownProvider>
            <Dropdown
                wrapperClass="relative"
                dropdownClasses="right-0 min-w-[160px]"
                useDefaultButtonClasses={false}
                buttonClass="
flex items-center justify-center p-2 h-8 w-8 text-sm font-semibold rounded transition-default
dropdown-button focus:outline-none space-x-1.5
px-3 bg-white border border-theme-secondary-300 hover:text-theme-secondary-700
dark:bg-theme-dark-900 dark:border-theme-dark-700 dark:text-theme-dark-200
dim:bg-theme-dark-900 dim:hover:bg-theme-dark-700
"
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
