"use client";

import { useEffect, useRef, useState } from "react";
import TableSortingContext from "./TableSortingContext";
import { ITableSortingContextType } from "./types";
import { router } from "@inertiajs/react";
import { usePageHandler } from "../PageHandler/PageHandlerContext";
import { SortDirection } from "@/types/generated";

export default function TableSortingProvider({
    initialSortBy,
    initialSortDirection,
    children,
    onChange,
}: {
    initialSortBy: string;
    initialSortDirection: SortDirection;
    children: React.ReactNode;
    onChange: (sortBy: string) => void;
}) {
    const isMounting = useRef(false);

    useEffect(() => {
        isMounting.current = true;
    }, []);

    const { refreshPage } = usePageHandler();

    const urlParams = new URLSearchParams(location.search);
    const initialSortByFromUrl = urlParams.get("sort") ?? initialSortBy;
    const initialSortDirectionFromUrl = (urlParams.get("sort-direction") as SortDirection) ?? initialSortDirection;

    const [sortBy, setSortBy] = useState<string>(initialSortByFromUrl);
    const [sortDirection, setSortDirection] = useState<SortDirection>(initialSortDirectionFromUrl);

    useEffect(() => {
        if (isMounting.current) {
            isMounting.current = false;

            return;
        }

        const updatedUrl = new URL(location.href);
        if (sortBy === initialSortBy) {
            updatedUrl.searchParams.delete("sort");
        } else {
            updatedUrl.searchParams.set("sort", sortBy);
        }

        if (sortDirection === initialSortDirection) {
            updatedUrl.searchParams.delete("sort-direction");
        } else {
            updatedUrl.searchParams.set("sort-direction", sortDirection);
        }

        updatedUrl.searchParams.delete("page");

        router.push({
            url: updatedUrl.toString(),
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                refreshPage(() => {
                    onChange(sortBy);
                });
            },
        });
    }, [sortBy, sortDirection]);

    const sort = (key: string) => {
        if (sortBy === key) {
            setSortDirection((prev: SortDirection) => {
                return prev === SortDirection.ASC ? SortDirection.DESC : SortDirection.ASC;
            });

            return;
        }

        setSortBy(key);
        setSortDirection(SortDirection.ASC);
    };

    const value: ITableSortingContextType = {
        sort,
        sortBy,
        sortDirection,
    };

    return <TableSortingContext.Provider value={value}>{children}</TableSortingContext.Provider>;
}
