export interface ITabsContextType {
    currentTab: string;
    selectedTab?: ITab;
    select: (value: string) => void;
    selectPrevious: () => void;
    selectNext: () => void;
}

export interface ITab {
    text: string;
    value: string;
}
