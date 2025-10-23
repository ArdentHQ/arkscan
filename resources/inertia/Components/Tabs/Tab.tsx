import { useTabs } from "@/Providers/Tabs/TabsContext";
import { ITab } from "@/Providers/Tabs/types"
import classNames from "@/utils/class-names"

export default function Tab({ text, value, withDivider }: ITab & { withDivider?: boolean } ) {
    const { currentTab, select, selectPrevious, selectNext } = useTabs();

    return (
        <button
            type="button"
            className="flex relative items-center pr-6 space-x-6 cursor-pointer first:pl-4 last:pr-4 transition-default dark:hover:text-theme-secondary-200 hover:text-theme-secondary-900"
            onClick={() => select(value)}
            onKeyDown={(e) => {
                if (e.key === ' ' || e.key === 'Enter') {
                    select(value);

                    if (e.key === ' ') {
                        e.preventDefault();
                    }

                    return;
                }

                if (e.key === 'ArrowLeft') {
                    selectPrevious();

                    return;
                }

                if (e.key === 'ArrowRight') {
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
            {withDivider && (
                <div className="w-px h-5 bg-theme-secondary-300 dark:bg-theme-dark-800"></div>
            )}

            <span className={classNames({
                "block pt-4 pb-3 w-full h-full font-semibold whitespace-nowrap border-b-2": true,
                'border-transparent dark:text-theme-dark-200': currentTab !== value,
                'text-theme-secondary-900 border-theme-primary-600 dark:text-theme-dark-50 dim:border-theme-dark-blue-600': currentTab === value,
            })}>
                {text}
            </span>
        </button>
    );
}
