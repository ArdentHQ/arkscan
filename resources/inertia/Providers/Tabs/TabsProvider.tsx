"use client";

import { useEffect, useState } from "react";
import TabsContext from "./TabsContext";
import { ITab, ITabsContextType } from "./types";
import Wrapper from "@/Components/Tabs/Wrapper";
import { router } from "@inertiajs/react";

export default function TabsProvider({
    defaultSelected,
    tabs,
    children,
}: {
    defaultSelected: string;
    tabs: ITab[];
    children: React.ReactNode;
}) {
    const [currentTab, setCurrentTab] = useState<string>();
    const [selectedTab, setSelectedTab] = useState<ITab>();
    const [onChange, setOnChange] = useState<((newTab: ITab) => void) | null>(null);

    useEffect(() => {
        const tab = new URL(location.href).searchParams.get("tab") ?? defaultSelected;
        if (tab) {
            const tabEntry = tabs.find((t) => t.value === tab);
            if (!tabEntry) {
                return;
            }

            setCurrentTab(tab);
            setSelectedTab(tabs.find((t) => t.value === tab) ?? tabs[0]);

            if (onChange) {
                onChange(tabEntry);
            }
        }
    }, []);

    useEffect(() => {
        if (!currentTab) {
            return;
        }

        const updatedUrl = new URL(location.href);
        updatedUrl.searchParams.set("tab", currentTab);

        router.push({
            url: updatedUrl.toString(),
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                const newTab = tabs.find((tab) => tab.value === currentTab) ?? tabs[0];
                setSelectedTab(newTab);

                if (onChange) {
                    onChange(newTab);
                }
            },
        });
    }, [currentTab]);

    const value: ITabsContextType = {
        currentTab,
        selectedTab,
        select: (value: string) => {
            setCurrentTab(value);
        },
        selectPrevious: () => {
            const currentIndex = tabs.findIndex((tab) => tab.value === currentTab);
            const previousIndex = (currentIndex - 1 + tabs.length) % tabs.length;
            setCurrentTab(tabs[previousIndex].value);
            setSelectedTab(tabs[previousIndex]);
        },
        selectNext: () => {
            const currentIndex = tabs.findIndex((tab) => tab.value === currentTab);
            const nextIndex = (currentIndex + 1) % tabs.length;
            setCurrentTab(tabs[nextIndex].value);
            setSelectedTab(tabs[nextIndex]);
        },
        onTabChange: (callback: CallableFunction) => {
            setOnChange(() => callback);
        },
    };

    return (
        <TabsContext.Provider value={value}>
            <Wrapper tabs={tabs} />

            <div>{children}</div>
        </TabsContext.Provider>
    );
}
