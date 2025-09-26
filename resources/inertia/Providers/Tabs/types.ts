export interface ITabsContextType {
    currentTab: string;
    select: (value: string) => void;
    selectPrevious: () => void;
    selectNext: () => void;
}

export interface ITab {
    text: string;
    value: string;
}
