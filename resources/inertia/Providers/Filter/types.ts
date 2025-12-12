import { IFilters } from "@/types";

export interface IFilterContextType {
    selectedFilters: IFilters;
    setFilter: (key: string, checked: boolean) => void;
    setSelectedFilters: (filters: IFilters) => void;
    initialOptions: IFilterEntry[];
}

export interface IFilterOptionEntry {
    label: string;
    value: string;
    selected: boolean;
}

export interface IFilterGroupEntry {
    label: string;
    options: IFilterOptionEntry[];
}

export type IFilterEntry = IFilterOptionEntry | IFilterGroupEntry;
