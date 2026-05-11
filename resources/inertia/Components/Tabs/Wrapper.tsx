import { ITab } from "@/Providers/Tabs/types";
import Tab from "./Tab";
import { useEffect, useRef, useState } from "react";

export default function Wrapper({ tabs, ariaLabel }: { tabs: ITab[]; ariaLabel?: string }) {
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
        <div className="relative px-0 md:mb-3 md:px-10 lg:mx-auto lg:max-w-7xl">
            {showOverflowIndicators && (
                <>
                    <div className="to-theme-secondary-200/0 dark:to-theme-dark-950/0 from-theme-secondary-200 dark:from-theme-dark-950 pointer-events-none absolute top-0 left-0 z-20 h-12 h-full w-12 bg-gradient-to-r"></div>
                    <div className="to-theme-secondary-200/0 dark:to-theme-dark-950/0 from-theme-secondary-200 dark:from-theme-dark-950 pointer-events-none absolute top-0 right-0 z-20 h-12 h-full w-12 bg-gradient-to-l"></div>
                </>
            )}

            <div
                ref={containerRef}
                className="no-scrollbar bg-theme-secondary-200 dark:bg-theme-dark-950 mb-6 w-screen overflow-scroll px-6 py-2 md:mb-4 md:w-auto md:bg-transparent! md:px-0 md:py-0 lg:mb-0"
            >
                <div className="bg-theme-secondary-200 dark:bg-theme-dark-950 relative z-10 inline-flex items-center justify-between rounded-xl md:p-1">
                    <div role="tablist" aria-label={ariaLabel} className="flex space-x-1 px-0! pr-6 md:pr-0">
                        {tabs.map((tab) => (
                            <Tab key={tab.value} text={tab.text} value={tab.value} />
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
