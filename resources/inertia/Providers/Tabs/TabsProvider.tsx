"use client";

import { useEffect, useState } from "react";
import TabsContext from "./TabsContext";
import { ITab, ITabsContextType, ITabsQueryString, TabChangedMethod } from "./types";
import Wrapper from "@/Components/Tabs/Wrapper";
import { router } from "@inertiajs/react";

export default function TabsProvider({
    defaultSelected,
    queryStringDefaults,
    tabs,
    children,
}: {
    defaultSelected: string;
    queryStringDefaults: ITabsQueryString;
    tabs: ITab[];
    children: React.ReactNode;
}) {
    const [currentTab, setCurrentTab] = useState<string>();
    const [selectedTab, setSelectedTab] = useState<ITab>();
    const [onChange, setOnChange] = useState<TabChangedMethod | null>(null);
    const [queryStringValues, setQueryStringValues] = useState<ITabsQueryString>(queryStringDefaults);
    const [tabLoaded, setTabLoaded] = useState<Record<string, boolean>>({});

    const changeTab = (newTab: string) => {
        if (currentTab) {
            const updatedQueryStringValues = { ...queryStringValues[currentTab] };

            const currentUrl = new URL(location.href);
            Object.entries(queryStringDefaults[currentTab]).forEach(([param, value]) => {
                updatedQueryStringValues[param] = (currentUrl.searchParams.get(param) ?? value).toString();
            });

            setQueryStringValues({
                ...queryStringValues,
                [currentTab]: updatedQueryStringValues,
            });
        }

        setCurrentTab(newTab);
    };

    const changeTabUrl = (newTab: string) => {
        const updatedUrl = new URL(location.href);
        updatedUrl.search = "";

        if (newTab !== defaultSelected) {
            updatedUrl.searchParams.set("tab", newTab);
        }

        Object.entries(queryStringValues[newTab]).forEach(([param, value]) => {
            const defaultValue = String(queryStringDefaults[newTab][param]);

            if (String(value) !== defaultValue) {
                updatedUrl.searchParams.set(param, String(value));
            }
        });

        router.push({
            url: updatedUrl.toString(),
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                const tabObject = tabs.find((tab) => tab.value === newTab) ?? tabs[0];
                setSelectedTab(tabObject);

                if (onChange) {
                    onChange(tabObject, tabLoaded[newTab] !== true);

                    if (tabLoaded[newTab] !== true) {
                        setTabLoaded({ ...tabLoaded, [newTab]: true });
                    }
                }
            },
        });
    };

    useEffect(() => {
        const tab = new URL(location.href).searchParams.get("tab") ?? defaultSelected;

        if (tab) {
            const tabEntry = tabs.find((t) => t.value === tab);
            if (!tabEntry) {
                return;
            }

            setCurrentTab(tab);
            setSelectedTab(tabs.find((t) => t.value === tab) ?? tabs[0]);
            setTabLoaded({ ...tabLoaded, [tab]: true });
        }
    }, []);

    useEffect(() => {
        if (!currentTab) {
            return;
        }

        changeTabUrl(currentTab);
    }, [currentTab]);

    const value: ITabsContextType = {
        currentTab,
        selectedTab,
        select: (value: string) => {
            changeTab(value);
        },
        selectPrevious: () => {
            const currentIndex = tabs.findIndex((tab) => tab.value === currentTab);
            const previousIndex = (currentIndex - 1 + tabs.length) % tabs.length;
            changeTab(tabs[previousIndex].value);
        },
        selectNext: () => {
            const currentIndex = tabs.findIndex((tab) => tab.value === currentTab);
            const nextIndex = (currentIndex + 1) % tabs.length;
            changeTab(tabs[nextIndex].value);
        },
        onTabChange: (callback: TabChangedMethod) => {
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
