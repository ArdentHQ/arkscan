import { useTabs } from "@/Providers/Tabs/TabsContext";
import { ITab } from "@/Providers/Tabs/types";
import classNames from "classnames";

export default function Tab({ text, value }: ITab) {
    const { currentTab, select, selectPrevious, selectNext } = useTabs();

    return (
        <button
            type="button"
            className="transition-default group/tab relative flex cursor-pointer items-center text-theme-secondary-700 hover:text-theme-secondary-900 dark:text-theme-dark-200 dark:hover:text-theme-secondary-200"
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
            <span
                className={classNames({
                    "transition-default block h-full w-full whitespace-nowrap rounded px-3 py-1.5 font-semibold sm:rounded-lg": true,
                    "group-hover/tab:bg-theme-secondary-300 group-hover/tab:text-theme-secondary-900 dark:text-theme-dark-200 dark:group-hover/tab:bg-theme-dark-900 dark:group-hover/tab:text-theme-dark-50":
                        currentTab !== value,
                    "bg-white text-theme-secondary-900 dark:bg-theme-dark-800 dark:text-theme-dark-50":
                        currentTab === value,
                })}
            >
                {text}
            </span>
        </button>
    );
}
