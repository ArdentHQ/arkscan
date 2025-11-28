import { useTabs } from "@/Providers/Tabs/TabsContext";
import { ITab } from "@/Providers/Tabs/types";
import classNames from "classnames";

export default function Tab({ text, value, withDivider }: ITab & { withDivider?: boolean }) {
    const { currentTab, select, selectPrevious, selectNext } = useTabs();

    return (
        <button
            type="button"
            className="transition-default relative flex cursor-pointer items-center space-x-6 pr-6 first:pl-4 last:pr-4 hover:text-theme-secondary-900 dark:hover:text-theme-secondary-200"
            onClick={() => select(value)}
            onKeyDown={(e) => {
                if (e.key === " " || e.key === "Enter") {
                    select(value);

                    if (e.key === " ") {
                        e.preventDefault();
                    }

                    return;
                }

                if (e.key === "ArrowLeft") {
                    selectPrevious();

                    return;
                }

                if (e.key === "ArrowRight") {
                    selectNext();

                    return;
                }
            }}
            role="tab"
            id={`tab-${value}`}
            aria-controls={`panel-${value}`}
            tabIndex={currentTab === value ? 0 : -1}
            aria-selected={currentTab === value}
        >
            {withDivider && <div className="h-5 w-px bg-theme-secondary-300 dark:bg-theme-dark-800"></div>}

            <span
                className={classNames({
                    "block h-full w-full whitespace-nowrap border-b-2 pb-3 pt-4 font-semibold": true,
                    "border-transparent dark:text-theme-dark-200": currentTab !== value,
                    "border-theme-primary-600 text-theme-secondary-900 dim:border-theme-dark-blue-600 dark:text-theme-dark-50":
                        currentTab === value,
                })}
            >
                {text}
            </span>
        </button>
    );
}
