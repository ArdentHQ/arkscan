export interface ITabsContextType {
    currentTab?: string;
    selectedTab?: ITab;
    select: (value: string) => void;
    selectPrevious: () => void;
    selectNext: () => void;
    onTabChange: (callback: TabChangedMethod) => void;
    addEventListener: (event: string, callback: (tab: ITab) => void) => void;
    removeEventListener: (event: string, callback: (tab: ITab) => void) => void;
}

export interface ITab {
    text: string;
    value: string;
}

export interface ITabsQueryString {
    [key: string]: Record<string, string | number | boolean>;
}

export type TabChangedMethod = (newTab: ITab, isFirstLoad: boolean) => void;
