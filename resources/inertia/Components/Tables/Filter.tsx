// import { useDropdown } from "@/Providers/Dropdown/DropdownContext";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import FilterIcon from "@ui/icons/filter.svg?react";
import { useTranslation } from "react-i18next";
import Dropdown from "../General/Dropdown/Dropdown";
import DropdownCheckboxItem from "../General/Dropdown/DropdownCheckboxItem";
import { IFilters } from "@/types";
import { useFilter } from "@/Providers/Filter/FilterContext";
import { IFilterEntry, IFilterOptionEntry } from "@/Providers/Filter/types";

function FilterComponent({ disabled = false, withSelectAll = true }: { disabled?: boolean; withSelectAll?: boolean }) {
    const { t } = useTranslation();

    const { selectedFilters, setFilter, setSelectedFilters, initialOptions } = useFilter();

    return (
        <Dropdown
            dropdownContentClasses="bg-white dark:bg-theme-dark-900 border border-white dark:border-theme-dark-700 px-1 py-0.5 rounded-xl"
            dropdownClasses="px-6 w-full md:px-8 table-filter md:w-[303px]"
            disabled={disabled}
            useDefaultButtonClasses={false}
            closeOnClick={false}
            buttonClass="button-secondary flex w-full flex-1 items-center justify-center rounded py-1.5 sm:flex-none sm:px-4 md:p-2"
            zIndex={30}
            button={
                <div className="mx-auto inline-flex items-center whitespace-nowrap">
                    <FilterIcon className="h-4 w-4" />

                    <div className="ml-2 md:hidden">{t("actions.filter")}</div>
                </div>
            }
        >
            {withSelectAll && (
                <SelectAllOption selectedFilters={selectedFilters} setSelectedFilters={setSelectedFilters} />
            )}

            {initialOptions.map((option) => (
                <FilterOption
                    key={"options" in option ? option.label : option.value}
                    filterOption={option}
                    selectedFilters={selectedFilters}
                    setFilter={setFilter}
                />
            ))}
        </Dropdown>
    );
}

function SelectAllOption({
    selectedFilters,
    setSelectedFilters,
}: {
    selectedFilters: IFilters;
    setSelectedFilters: (filters: IFilters) => void;
}) {
    const { t } = useTranslation();

    return (
        <DropdownCheckboxItem
            id="filter-select-all"
            name="filter-select-all"
            onClick={(checked) => {
                const newSelectedFilters = { ...selectedFilters };

                for (const key in newSelectedFilters) {
                    newSelectedFilters[key] = checked;
                }

                setSelectedFilters(newSelectedFilters);
            }}
            checked={
                Object.values(selectedFilters).filter((value) => value === true).length ===
                Object.keys(selectedFilters).length
            }
        >
            {t("tables.filters.select_all")}
        </DropdownCheckboxItem>
    );
}

function FilterOption({
    filterOption,
    selectedFilters,
    setFilter,
}: {
    filterOption: IFilterEntry;
    selectedFilters: IFilters;
    setFilter: (key: string, checked: boolean) => void;
}) {
    if ("options" in filterOption) {
        return (
            <div className="mt-1 border-t border-theme-secondary-300 pt-[0.125rem] dark:border-theme-dark-800">
                <div className="group mb-1 mt-3 px-8 font-semibold leading-5 text-theme-secondary-900 dark:text-theme-dark-200 md:px-4">
                    {filterOption.label}
                </div>

                <div className="flex flex-col">
                    {filterOption.options.map((option: IFilterOptionEntry) => (
                        <DropdownCheckboxItem
                            key={option.value}
                            id={`filter-${option.value}`}
                            name={`filter-${option.value}`}
                            onClick={(checked) => setFilter(option.value, checked)}
                            checked={selectedFilters[option.value] === true}
                        >
                            {option.label}
                        </DropdownCheckboxItem>
                    ))}
                </div>
            </div>
        );
    }

    return (
        <>
            <DropdownCheckboxItem
                key={filterOption.value}
                id={`filter-${filterOption.value}`}
                name={`filter-${filterOption.value}`}
                onClick={(checked) => setFilter(filterOption.value, checked)}
                checked={selectedFilters[filterOption.value] === true}
            >
                {filterOption.label}
            </DropdownCheckboxItem>
        </>
    );
}

export default function Filter({
    disabled = false,
    withSelectAll = false,
}: {
    disabled?: boolean;
    withSelectAll?: boolean;
}) {
    return (
        <DropdownProvider>
            <FilterComponent disabled={disabled} withSelectAll={withSelectAll} />
        </DropdownProvider>
    );
}
