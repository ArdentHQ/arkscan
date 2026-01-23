import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import Dropdown from "../General/Dropdown/Dropdown";
import classNames from "classnames";
import ChevronDownSmallIcon from "@ui/icons/arrows/chevron-down-small.svg?react";
import DropdownItem from "../General/Dropdown/DropdownItem";
import React, { createElement } from "react";
import { ExchangeDropdownItem } from "@/Pages/Exchanges.contracts";

export default function ExchangeDropdown({
    icon,
    items,
    children,
    onChange,
}: {
    icon: React.ElementType;
    items: ExchangeDropdownItem[];
    children: React.ReactNode;
    onChange: (value: string) => void;
}) {
    return (
        <DropdownProvider>
            <Dropdown
                wrapperClass="relative h-full flex-1 md:w-50"
                buttonClass="inline-flex h-full bg-white rounded border border-theme-secondary-300 dark:bg-theme-dark-900 dark:border-theme-dark-700 py-3 px-4 w-full"
                dropdownClasses="w-full md:w-auto"
                popupStyles={{ width: "100%" }}
                placement="bottom-start"
                button={({ isOpen }) => (
                    <div
                        className={classNames(
                            "transition-default flex w-full items-center justify-between rounded font-semibold text-theme-secondary-900 dark:text-theme-dark-50 md-lg:w-50",
                        )}
                    >
                        <div className="flex items-center space-x-2">
                            {createElement(icon, {
                                className:
                                    "text-theme-secondary-700 dark:text-theme-dark-300 group-hover:dark:text-theme-dark-200 w-5 h-5",
                            })}

                            <span>{children}</span>
                        </div>

                        <span
                            className={classNames(
                                "ml-2 text-theme-secondary-700 transition duration-150 ease-in-out dark:text-theme-dark-50",
                                {
                                    "rotate-180": isOpen,
                                },
                            )}
                        >
                            <ChevronDownSmallIcon className="h-3 w-3" />
                        </span>
                    </div>
                )}
            >
                {items.map((child: ExchangeDropdownItem, index) => (
                    <DropdownItem asChild key={index} onClick={() => onChange(child.value)}>
                        <div>{child.title}</div>
                    </DropdownItem>
                ))}
            </Dropdown>
        </DropdownProvider>
    );
}
