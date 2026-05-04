import Dropdown from "@/Components/General/Dropdown/Dropdown";
import DropdownItem from "@/Components/General/Dropdown/DropdownItem";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import ChevronDownSmallIcon from "@ui/icons/arrows/chevron-down-small.svg?react";
import classNames from "classnames";

export default function MobileDropdown({
    items,
    active,
    onSelect,
}: {
    items: Array<{ value: string; label: string }>;
    active: string;
    onSelect: (value: string) => void;
}) {
    const activeLabel = items.find((item) => item.value === active)?.label;

    return (
        <div className="mb-5 px-6 md:hidden">
            <DropdownProvider>
                <Dropdown
                    wrapperClass="relative w-full rounded border border-theme-secondary-300 dark:border-theme-dark-800 dark:text-theme-dark-200 md:w-1/2"
                    dropdownClasses="left-0 w-full z-20"
                    dropdownContentClasses="rounded-xl bg-white shadow-lg dark:bg-theme-dark-800 dark:shadow-none"
                    buttonClass="w-full justify-between py-3 px-4 text-left font-semibold text-theme-secondary-900 dark:text-theme-dark-200"
                    useDefaultButtonClasses={false}
                    button={({ isOpen }) => (
                        <div className="flex w-full items-center justify-between">
                            <span>{activeLabel}</span>
                            <ChevronDownSmallIcon
                                className={classNames(
                                    "transition-default text-theme-secondary-700 dark:text-theme-dark-200 h-3 w-3",
                                    {
                                        "rotate-180": isOpen,
                                    },
                                )}
                            />
                        </div>
                    )}
                >
                    <div className="block items-center justify-center py-2">
                        {items.map((item) => (
                            <DropdownItem
                                key={item.value}
                                selected={active === item.value}
                                onClick={() => onSelect(item.value)}
                            >
                                {item.label}
                            </DropdownItem>
                        ))}
                    </div>
                </Dropdown>
            </DropdownProvider>
        </div>
    );
}
