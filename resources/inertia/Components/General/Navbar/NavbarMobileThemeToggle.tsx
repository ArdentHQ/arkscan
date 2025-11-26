import classNames from "classnames";
import useSettings from "@/Providers/Settings/useSettings";
import SunIcon from "@ui/icons/sun.svg?react";
import MoonIcon from "@ui/icons/moon.svg?react";
import MoonStarsIcon from "@ui/icons/moon-stars.svg?react";

const NavbarMobileButton = ({
    selected,
    children,
    ...props
}: React.ButtonHTMLAttributes<HTMLButtonElement> & { selected: boolean }) => {
    return (
        <div className="relative p-2 pr-1 last:pr-2">
            {selected && (
                <div className="transition-default absolute left-1 top-1 h-6 w-6 rounded bg-white dark:bg-theme-dark-700"></div>
            )}

            <button
                className={classNames("relative z-10", {
                    "dark:text-theme-dark-300": !selected,
                    "text-theme-secondary-900 dark:text-theme-dark-50": selected,
                })}
                {...props}
            >
                {children}
            </button>
        </div>
    );
};

export default function NavbarMobileThemeToggle() {
    const { theme, updateTheme } = useSettings();

    return (
        <div>
            <div className="relative h-8 rounded bg-theme-secondary-200 dark:bg-theme-dark-900">
                <div className="flex items-center">
                    <NavbarMobileButton selected={theme === "light"} onClick={() => updateTheme("light")}>
                        <SunIcon className="h-4 w-4" />
                    </NavbarMobileButton>

                    <NavbarMobileButton selected={theme === "dark"} onClick={() => updateTheme("dark")}>
                        <MoonIcon className="h-4 w-4" />
                    </NavbarMobileButton>

                    <NavbarMobileButton selected={theme === "dim"} onClick={() => updateTheme("dim")}>
                        <MoonStarsIcon className="h-4 w-4" />
                    </NavbarMobileButton>
                </div>
            </div>
        </div>
    );
}
