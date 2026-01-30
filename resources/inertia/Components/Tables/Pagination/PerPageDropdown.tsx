import Dropdown from "@/Components/General/Dropdown/Dropdown";
import DropdownItem from "@/Components/General/Dropdown/DropdownItem";
import { useDropdown } from "@/Providers/Dropdown/DropdownContext";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import { IPaginatedResponse } from "@/types";
import classNames from "classnames";
import { useEffect, useState } from "react";
import ChevronDownSmallIcon from "@ui/icons/arrows/chevron-down-small.svg?react";
import { useTranslation } from "react-i18next";

function PerPageComponent({
    disabled = false,
    paginator,
    onChange,
}: {
    disabled?: boolean;
    paginator: IPaginatedResponse<any>;
    onChange: (perPage: number) => void;
}) {
    const { t } = useTranslation();
    const [perPage, setPerPage] = useState<number>();
    const { isOpen } = useDropdown();
    const perPageOptions =
        paginator.perPageOptions ?? (t("pagination.per_page_options", { returnObjects: true }) as number[]);

    useEffect(() => {
        if (perPage !== undefined) {
            onChange(perPage);
        }
    }, [perPage]);

    return (
        <Dropdown
            button={
                <div className="w-full">
                    <div className="transition-default flex items-center">
                        <div
                            className={classNames({
                                "transition-default flex items-center justify-center space-x-2 px-3 py-2 text-sm font-semibold leading-4": true,
                                "dark:text-theme-dark-50": disabled === false,
                                "dark:bg-theme-dark-800": disabled === true,
                            })}
                        >
                            <span>{paginator.per_page ?? perPage}</span>

                            <span
                                className={classNames({
                                    "transition-default": true,
                                    "rotate-180": isOpen,
                                })}
                            >
                                <ChevronDownSmallIcon className="h-3 w-3" />
                            </span>
                        </div>
                    </div>
                </div>
            }
            testId="pagination:per-page-dropdown"
        >
            {perPageOptions.map((option: number) => (
                <DropdownItem
                    key={option}
                    onClick={() => setPerPage(option)}
                    selected={(paginator.per_page ?? perPage) === option}
                >
                    {option}
                </DropdownItem>
            ))}
        </Dropdown>
    );
}

export default function PerPageDropdown({
    disabled = false,
    paginator,
    onChange,
}: {
    disabled?: boolean;
    paginator: IPaginatedResponse<any>;
    onChange: (perPage: number) => void;
}) {
    return (
        <DropdownProvider>
            <PerPageComponent disabled={disabled} paginator={paginator} onChange={onChange} />
        </DropdownProvider>
    );
}
