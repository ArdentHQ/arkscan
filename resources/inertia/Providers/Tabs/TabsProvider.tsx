import { useEffect, useState } from "react";
import TabsContext from "./TabsContext";
import { ITab, ITabsContextType, ITabsQueryString, TabChangedMethod } from "./types";
import Wrapper from "@/Components/Tabs/Wrapper";
import { router } from "@inertiajs/react";

export default function TabsProvider({
    defaultSelected,
    queryStringDefaults,
    tabs,
    baseUrl,
    header,
    children,
}: {
    defaultSelected: string;
    queryStringDefaults: ITabsQueryString;
    tabs: ITab[];
    baseUrl: string;
    header?: React.ReactNode;
    children: React.ReactNode;
}) {
    const [currentTab, setCurrentTab] = useState<string>();
    const [selectedTab, setSelectedTab] = useState<ITab>();
    const [onChange, setOnChange] = useState<TabChangedMethod | null>(null);
    const [queryStringValues, setQueryStringValues] = useState<ITabsQueryString>(queryStringDefaults);
    const [tabLoaded, setTabLoaded] = useState<Record<string, boolean>>({});
    const [events, setEvents] = useState<Record<string, ((tab: ITab) => void)[]>>({});

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
        const updatedUrl = new URL(baseUrl);
        updatedUrl.search = "";

        if (newTab !== defaultSelected) {
            updatedUrl.pathname += `/${newTab}`;
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

                Object.entries(events).forEach(([event, callbacks]) => {
                    if (event === "tabChange") {
                        callbacks.forEach((callback) => callback(tabObject));
                    }
                });

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
        let tab: string | null = null;

        const currentBaseUrl = `${location.protocol}//${location.hostname.replace(/\/$/, "")}/${location.pathname.replace(/^\//, "")}`;
        if (currentBaseUrl !== baseUrl) {
            tab = currentBaseUrl.replace(baseUrl + "/", "");
        }

        if (!tab) {
            tab = new URL(location.href).searchParams.get("view") ?? defaultSelected;
        }

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
        addEventListener: (event: string, callback: (tab: ITab) => void) => {
            const existingEvents = events[event] || [];
            setEvents({
                ...events,
                [event]: [...existingEvents, callback],
            });
        },
        removeEventListener: (event: string, callback: (tab: ITab) => void) => {
            const existingEvents = events[event] || [];
            setEvents({
                ...events,
                [event]: existingEvents.filter((cb) => cb !== callback),
            });
        },
    };

    return (
        <TabsContext.Provider value={value}>
            {header}

            <Wrapper tabs={tabs} />

            <div>{children}</div>
        </TabsContext.Provider>
    );
}
