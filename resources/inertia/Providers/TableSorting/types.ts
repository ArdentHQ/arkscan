import { SortDirection } from "@/types/generated";

export interface ITableSortingContextType {
    sort: (key: string) => void;
    sortBy: string;
    sortDirection: SortDirection;
}
