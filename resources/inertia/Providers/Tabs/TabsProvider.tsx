import { useEffect, useRef, useState } from "react";
import TabsContext from "./TabsContext";
import { ITab, ITabsContextType, ITabsQueryString, TabChangedMethod } from "./types";
import Wrapper from "@/Components/Tabs/Wrapper";
import { router } from "@inertiajs/react";

export function resolveTabQueryStringValues(
    defaults: ITabsQueryString,
    tab: string,
    url: URL,
): Record<string, string | number | boolean> {
    const tabDefaults = defaults[tab];

    return Object.fromEntries(
        Object.entries(tabDefaults).map(([param, value]) => [param, (url.searchParams.get(param) ?? value).toString()]),
    );
}

export default function TabsProvider({
    defaultSelected,
    queryStringDefaults,
    tabs,
    baseUrl,
    header,
    children,
    useQueryParam = false,
    viewParam = "view",
    ariaLabel,
}: {
    defaultSelected: string;
    queryStringDefaults: ITabsQueryString;
    tabs: ITab[];
    baseUrl: string;
    header?: React.ReactNode;
    children: React.ReactNode;
    useQueryParam?: boolean;
    viewParam?: string;
    ariaLabel?: string;
}) {
    const [currentTab, setCurrentTab] = useState<string>();
    const [selectedTab, setSelectedTab] = useState<ITab>();
    const [onChange, setOnChange] = useState<TabChangedMethod | null>(null);
    const [queryStringValues, setQueryStringValues] = useState<ITabsQueryString>(queryStringDefaults);
    const [tabLoaded, setTabLoaded] = useState<Record<string, boolean>>({});
    const [events, setEvents] = useState<Record<string, ((tab: ITab) => void)[]>>({});
    const hasMounted = useRef(false);

    const changeTab = (newTab: string) => {
        if (currentTab) {
            setQueryStringValues({
                ...queryStringValues,
                [currentTab]: resolveTabQueryStringValues(queryStringDefaults, currentTab, new URL(location.href)),
            });
        }

        setCurrentTab(newTab);
    };

    const changeTabUrl = (newTab: string) => {
        const updatedUrl = new URL(location.href);
        updatedUrl.search = "";
        updatedUrl.pathname = baseUrl;

        if (!useQueryParam && newTab !== defaultSelected) {
            updatedUrl.pathname += `/${newTab}`;
        }

        if (useQueryParam && newTab !== defaultSelected) {
            updatedUrl.searchParams.set(viewParam, newTab);
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

        if (!useQueryParam) {
            const currentBaseUrl = `/${location.pathname.replace(/^\//, "")}`;
            const normalizedCurrentBaseUrl = currentBaseUrl.toLowerCase();
            const normalizedBaseUrl = baseUrl.toLowerCase();

            if (normalizedCurrentBaseUrl !== normalizedBaseUrl) {
                const normalizedBaseWithSlash = normalizedBaseUrl.endsWith("/")
                    ? normalizedBaseUrl
                    : `${normalizedBaseUrl}/`;

                if (normalizedCurrentBaseUrl.startsWith(normalizedBaseWithSlash)) {
                    tab = currentBaseUrl.slice(normalizedBaseWithSlash.length);
                }
            }
        }

        if (!tab) {
            tab = new URL(location.href).searchParams.get(viewParam) ?? defaultSelected;
        }

        if (tab) {
            const tabEntry = tabs.find((t) => t.value === tab);
            if (!tabEntry) {
                return;
            }

            setQueryStringValues((prev) => ({
                ...prev,
                [tab]: resolveTabQueryStringValues(queryStringDefaults, tab, new URL(location.href)),
            }));

            setCurrentTab(tab);
            setSelectedTab(tabs.find((t) => t.value === tab) ?? tabs[0]);
            setTabLoaded({ ...tabLoaded, [tab]: true });
        }
    }, []);

    useEffect(() => {
        if (!currentTab) {
            return;
        }

        if (!hasMounted.current) {
            hasMounted.current = true;

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

            <Wrapper tabs={tabs} ariaLabel={ariaLabel} />

            <div
                role="tabpanel"
                id={currentTab ? `panel-${currentTab}` : undefined}
                aria-labelledby={currentTab ? `tab-${currentTab}` : undefined}
            >
                {children}
            </div>
        </TabsContext.Provider>
    );
}
