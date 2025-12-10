import { ITab } from "@/Providers/Tabs/types";
import Tab from "./Tab";
import { useEffect, useRef, useState } from "react";

export default function Wrapper({ tabs }: { tabs: ITab[] }) {
    const [showOverflowIndicators, setShowOverflowIndicators] = useState(false);
    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        function checkOverflow() {
            const container = containerRef.current;

            if (container) {
                setShowOverflowIndicators(container.scrollWidth > container.clientWidth);
            }
        }

        checkOverflow();

        window.addEventListener("resize", checkOverflow);

        return () => {
            window.removeEventListener("resize", checkOverflow);
        };
    }, []);

    return (
        <div
            x-data="{
                showOverflowIndicators: false,
                checkOverflow: function () {
                    const container = this.$refs.container;

                    this.showOverflowIndicators = container.scrollWidth > container.clientWidth;
                },
            }"
            className="relative px-0 sm:mb-3 sm:px-6 md:mx-auto md:max-w-7xl md:px-10"
            x-resize="checkOverflow()"
        >
            {showOverflowIndicators && (
                <>
                    <div className="to-theme-secondary-200/0 dark:to-theme-dark-950/0 pointer-events-none absolute left-0 top-0 z-20 h-12 h-full w-12 bg-gradient-to-r from-theme-secondary-200 dark:from-theme-dark-950"></div>
                    <div className="to-theme-secondary-200/0 dark:to-theme-dark-950/0 pointer-events-none absolute right-0 top-0 z-20 h-12 h-full w-12 bg-gradient-to-l from-theme-secondary-200 dark:from-theme-dark-950"></div>
                </>
            )}

            <div
                ref={containerRef}
                className="no-scrollbar mb-6 w-screen overflow-scroll bg-theme-secondary-200 px-6 py-2 dark:bg-theme-dark-950 sm:mb-4 sm:w-auto sm:!bg-transparent sm:px-0 sm:py-0 md:mb-0"
            >
                <div className="relative z-10 inline-flex items-center justify-between rounded-xl bg-theme-secondary-200 dark:bg-theme-dark-950 sm:p-1">
                    <div role="tablist" className="flex space-x-1 !px-0 pr-6 sm:pr-0">
                        {tabs.map((tab) => (
                            <Tab key={tab.value} text={tab.text} value={tab.value} />
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
