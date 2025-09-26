import { ITab } from "@/Providers/Tabs/types";
import Tab from "./Tab";

export default function Wrapper({ tabs }: { tabs: ITab[] }) {
    return (
        <div className="px-6 md:px-10 md:mx-auto md:max-w-7xl">
            <div className="items-center justify-between inline-flex bg-theme-secondary-100 rounded-xl dark:bg-black relative z-10 hidden mb-3 md:inline-flex">
                <div
                    role="tablist"
                    className="flex"
                >
                    {tabs.map((tab, index: number) => (
                        <Tab
                            key={tab.value}
                            text={tab.text}
                            value={tab.value}
                            withDivider={index > 0}
                        />
                    ))}
                </div>
            </div>
        </div>
    );
}
