import classNames from "classnames";
import { useTranslation } from "react-i18next";

export default function NavbarResults({ query }: { query: string }) {
    const { t } = useTranslation();

    const open = query.length > 0;

    const hasResults = false;

    return (
        <div
            className={classNames(
                "absolute right-0 top-9 z-10 mt-2 origin-top-right rounded-xl border border-transparent bg-white py-1 shadow-lg transition-all duration-150 dark:border-theme-dark-800 dark:bg-theme-dark-900 dark:text-theme-dark-200",
                {
                    "pointer-events-auto scale-100 opacity-100": open,
                    "pointer-events-none scale-95 opacity-0": !open,
                    "w-[560px]": !hasResults,
                    "w-[628px]": hasResults,
                },
            )}
        >
            {open && (
                <div className="custom-scroll flex max-h-[410px] flex-col space-y-1 divide-y divide-dashed divide-theme-secondary-300 overflow-y-auto whitespace-nowrap px-6 py-3 text-sm font-semibold dark:divide-theme-dark-800">
                    <p className="text-center text-theme-secondary-900 dark:text-theme-dark-50">
                        {t("general.search.no_results")}
                    </p>
                </div>
            )}
        </div>
    );
}
