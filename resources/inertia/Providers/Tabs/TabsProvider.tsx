'use client';

import { useState } from "react";
import TabsContext from "./TabsContext";
import { ITab, ITabsContextType } from "./types";
import Wrapper from "@/Components/Tabs/Wrapper";

export default function TabsProvider({
    defaultSelected,
    tabs,
    children,
}: {
    defaultSelected: string;
    tabs: ITab[];
    children: React.ReactNode;
}) {
    const [currentTab, setCurrentTab] = useState<string>(defaultSelected);

    const value: ITabsContextType = {
        currentTab,
        select: (value: string) => setCurrentTab(value),
        selectPrevious: () => {
            const currentIndex = tabs.findIndex(tab => tab.value === currentTab);
            const previousIndex = (currentIndex - 1 + tabs.length) % tabs.length;
            setCurrentTab(tabs[previousIndex].value);
        },
        selectNext: () => {
            const currentIndex = tabs.findIndex(tab => tab.value === currentTab);
            const nextIndex = (currentIndex + 1) % tabs.length;
            setCurrentTab(tabs[nextIndex].value);
        }
    };

    return (
        <TabsContext.Provider value={value}>
            <Wrapper tabs={tabs} />

            <div>{children}</div>
        </TabsContext.Provider>
    );
};
