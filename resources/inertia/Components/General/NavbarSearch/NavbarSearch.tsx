import { useTranslation } from "react-i18next";
import MagnifyingGlassSmallIcon from "@ui/icons/magnifying-glass-small.svg?react";
import CrossIcon from "@ui/icons/cross.svg?react";
import SquareReturnArrowIcon from "@ui/icons/square-return-arrow.svg?react";
import NavbarResults from "./NavbarResults";
import { useState } from "react";

export default function NavbarSearch() {
    const { t } = useTranslation();
    const [query, setQuery] = useState("");

    const clear = () => {
        setQuery("");
    };

    return (
        <div className="relative w-full">
            <div className="transition-default group w-[340px] rounded-md border border-transparent bg-theme-secondary-200 focus-within:border-theme-primary-600 focus-within:bg-white hover:bg-white dark:bg-theme-dark-900 focus-within:dark:border-theme-primary-600 md:w-full md-lg:w-[340px] hover:[&:not(:focus-within)]:border-theme-primary-600 hover:[&:not(:focus-within)]:dark:border-theme-dark-700">
                <div className="relative flex items-center rounded border border-transparent pl-1 focus-within:border-theme-primary-600 dark:border-theme-dark-700 focus-within:dark:border-theme-primary-600 hover:[&:not(:focus-within)]:border-theme-primary-600 group-hover:[&:not(:focus-within)]:dark:border-theme-dark-700">
                    <span className="ml-3 text-theme-secondary-500 dark:text-theme-dark-500">
                        <MagnifyingGlassSmallIcon className="h-4 w-4" />
                    </span>

                    <input
                        value={query}
                        onChange={(e) => setQuery(e.target.value)}
                        type="text"
                        id="search"
                        name="search"
                        autoComplete="off"
                        placeholder={t("general.navbar.search_placeholder")}
                        className="block w-full appearance-none rounded border-0 bg-transparent px-2 py-[7px] text-sm leading-4 text-theme-secondary-900 outline-none placeholder:text-theme-secondary-700 dark:text-theme-dark-50"
                    />

                    {query && (
                        <div className="mr-4 flex items-center space-x-4">
                            <button
                                type="button"
                                className="button-secondary -my-px bg-transparent p-2 text-theme-secondary-700 dark:bg-theme-dark-900 dark:text-theme-dark-200 dark:shadow-none"
                                onClick={clear}
                            >
                                <CrossIcon className="h-3 w-3" />
                            </button>

                            <SquareReturnArrowIcon className="hidden h-4 w-4 dark:text-theme-dark-200 sm:block" />
                        </div>
                    )}
                </div>
            </div>

            <NavbarResults
                onOpenChange={(open) => {
                    if (!open) clear();
                }}
                query={query}
            />
        </div>
    );
}
