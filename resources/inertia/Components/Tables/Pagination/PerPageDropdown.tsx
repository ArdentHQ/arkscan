import Dropdown from "@/Components/General/Dropdown/Dropdown";
import DropdownItem from "@/Components/General/Dropdown/DropdownItem";
import { useDropdown } from "@/Providers/Dropdown/DropdownContext";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import { IPaginatedResponse } from "@/types";
import classNames from "@/utils/class-names";
import { useEffect, useState } from "react";
import ChevronDownSmallIcon from "@ui/icons/arrows/chevron-down-small.svg?react";

function PerPageComponent({
    disabled = false,
    paginator,
    onChange,
}: {
    disabled?: boolean;
    paginator: IPaginatedResponse<any>;
    onChange: (perPage: number) => void;
}) {
    const [perPage, setPerPage] = useState<number>();
    const { isOpen } = useDropdown();

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
        >
            <DropdownItem onClick={() => setPerPage(10)} selected={(paginator.per_page ?? perPage) === 10}>
                10
            </DropdownItem>

            <DropdownItem onClick={() => setPerPage(25)} selected={(paginator.per_page ?? perPage) === 25}>
                25
            </DropdownItem>

            <DropdownItem onClick={() => setPerPage(50)} selected={(paginator.per_page ?? perPage) === 50}>
                50
            </DropdownItem>

            <DropdownItem onClick={() => setPerPage(100)} selected={(paginator.per_page ?? perPage) === 100}>
                100
            </DropdownItem>
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
