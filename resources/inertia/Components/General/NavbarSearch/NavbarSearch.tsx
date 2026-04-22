import { useTranslation } from "react-i18next";
import MagnifyingGlassSmallIcon from "@ui/icons/magnifying-glass-small.svg?react";
import CrossIcon from "@ui/icons/cross.svg?react";
import SquareReturnArrowIcon from "@ui/icons/square-return-arrow.svg?react";
import NavbarResults, { getResultHref } from "./NavbarResults";
import { type KeyboardEvent, useRef } from "react";
import { useNavbar } from "@/Components/General/Navbar/NavbarContext";
import { router } from "@inertiajs/react";
import { useFloating, autoUpdate, offset, shift, flip } from "@floating-ui/react";

export default function NavbarSearch() {
    const { t } = useTranslation();

    const { query, setQuery, results, clear } = useNavbar();

    const searchRef = useRef<HTMLDivElement>(null);

    const { refs, floatingStyles } = useFloating({
        placement: "bottom-end",
        whileElementsMounted: autoUpdate,
        middleware: [offset(8), shift({ padding: 16 }), flip({ padding: 8 })],
    });

    const blurHandler = (event: React.FocusEvent<HTMLElement>) => {
        const blurredOutside = !searchRef.current?.contains(event.relatedTarget);

        if (blurredOutside) {
            clear();
        }
    };

    const goToFirstResult = () => {
        if (results.length === 0) {
            return;
        }

        const firstResult = results[0];
        if (!firstResult) {
            return;
        }

        const href = getResultHref(firstResult);
        if (href === "#") {
            return;
        }

        router.visit(href);
    };

    const handleKeyDown = (event: KeyboardEvent<HTMLInputElement>) => {
        if (event.key === "Enter") {
            event.preventDefault();
            goToFirstResult();
        }

        if (event.key === "Escape") {
            clear();
        }
    };

    return (
        <div className="relative w-full" ref={searchRef}>
            <div
                ref={refs.setReference}
                className="transition-default group bg-theme-secondary-200 focus-within:border-theme-primary-600 dark:bg-theme-dark-900 focus-within:dark:border-theme-primary-600 md-lg:w-[340px] hover:[&:not(:focus-within)]:border-theme-primary-600 hover:[&:not(:focus-within)]:dark:border-theme-dark-700 w-[340px] rounded-md border border-transparent focus-within:bg-white hover:bg-white md:w-full"
            >
                <div className="focus-within:border-theme-primary-600 dark:border-theme-dark-700 focus-within:dark:border-theme-primary-600 hover:[&:not(:focus-within)]:border-theme-primary-600 group-hover:[&:not(:focus-within)]:dark:border-theme-dark-700 relative flex items-center rounded border border-transparent pl-1">
                    <span className="text-theme-secondary-500 dark:text-theme-dark-500 ml-3">
                        <MagnifyingGlassSmallIcon className="h-4 w-4" />
                    </span>

                    <input
                        value={query}
                        onChange={(e) => setQuery(e.target.value)}
                        onKeyDown={handleKeyDown}
                        type="text"
                        id="search"
                        name="search"
                        autoComplete="off"
                        placeholder={t("general.navbar.search_placeholder")}
                        className="text-theme-secondary-900 placeholder:text-theme-secondary-700 dark:text-theme-dark-50 block w-full appearance-none rounded border-0 bg-transparent px-2! py-[7px] text-sm leading-4 outline-none"
                        onBlur={blurHandler}
                    />

                    {query && (
                        <div className="mr-4 flex items-center space-x-4">
                            <button
                                type="button"
                                className="button-secondary text-theme-secondary-700 dark:bg-theme-dark-900 dark:text-theme-dark-200 -my-px bg-transparent p-2! dark:shadow-none"
                                onClick={clear}
                                aria-label="Clear search"
                            >
                                <CrossIcon className="h-3 w-3" />
                            </button>

                            <button
                                type="button"
                                onClick={goToFirstResult}
                                className="text-theme-secondary-700 dark:text-theme-dark-200 hidden sm:block"
                                aria-label={t("actions.submit")}
                            >
                                <SquareReturnArrowIcon className="h-4 w-4" />
                            </button>
                        </div>
                    )}
                </div>
            </div>

            <NavbarResults onBlur={blurHandler} floatingRef={refs.setFloating} floatingStyles={floatingStyles} />
        </div>
    );
}
