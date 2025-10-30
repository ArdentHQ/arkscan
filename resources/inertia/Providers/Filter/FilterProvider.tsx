"use client";

import { useEffect, useRef, useState } from "react";
import FilterContext from "./FilterContext";
import { IFilterContextType, IFilterEntry } from "./types";
import { IFilters } from "@/types";
import { router } from "@inertiajs/react";
import { usePageHandler } from "../PageHandler/PageHandlerContext";

function getFilterValuesFromOptions(initialOptions: IFilterEntry[], withQueryStringValue: boolean = false): IFilters {
    const urlParams = new URLSearchParams(location.search);

    return initialOptions.reduce((acc, option) => {
        if ("options" in option) {
            for (const groupOption of option.options) {
                const queryStringParamValue = urlParams.get(groupOption.value);
                if (withQueryStringValue && queryStringParamValue !== null) {
                    acc[groupOption.value] = queryStringParamValue === "true" ? true : false;

                    continue;
                }

                acc[groupOption.value] = groupOption.selected;
            }

            return acc;
        }

        acc[option.value] = option.selected;

        return acc;
    }, {} as IFilters);
}

export default function FilterProvider({
    initialOptions,
    children,
    onChange,
}: {
    initialOptions: IFilterEntry[];
    children: React.ReactNode;
    onChange: (filters: IFilters) => void;
}) {
    const isMounting = useRef(false);

    useEffect(() => {
        isMounting.current = true;
    }, []);

    const { refreshPage } = usePageHandler();

    const [selectedFilters, setSelectedFilters] = useState<IFilters>(getFilterValuesFromOptions(initialOptions, true));

    useEffect(() => {
        if (isMounting.current) {
            isMounting.current = false;

            return;
        }

        const updatedUrl = new URL(location.href);
        const defaultFilters = getFilterValuesFromOptions(initialOptions);
        for (const [key, value] of Object.entries(selectedFilters)) {
            if (value === defaultFilters[key]) {
                updatedUrl.searchParams.delete(key);

                continue;
            }

            updatedUrl.searchParams.set(key, value ? "true" : "false");
        }

        updatedUrl.searchParams.delete("page");

        router.push({
            url: updatedUrl.toString(),
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                refreshPage(() => {
                    onChange(selectedFilters);
                });
            },
        });
    }, [selectedFilters]);

    const setFilter = (key: string, checked: boolean) => {
        setSelectedFilters({
            ...selectedFilters,

            [key]: checked,
        });
    };

    const value: IFilterContextType = {
        selectedFilters,
        setFilter,
        setSelectedFilters,
        initialOptions,
    };

    return <FilterContext.Provider value={value}>{children}</FilterContext.Provider>;
}
