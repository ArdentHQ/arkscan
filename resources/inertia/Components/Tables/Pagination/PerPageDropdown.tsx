import ChevronDownSmall from "@/Assets/Icons/Arrows/ChevronDownSmall";
import Dropdown from "@/Components/General/Dropdown/Dropdown";
import DropdownItem from "@/Components/General/Dropdown/DropdownItem";
import { useDropdown } from "@/Providers/Dropdown/DropdownContext";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import { IPaginatedResponse } from "@/types";
import classNames from "@/utils/class-names";
import { useEffect, useState } from "react";

function PerPageComponent({
    disabled = false,
    paginator,
    onChange,
}: {
    disabled?: boolean;
    paginator: IPaginatedResponse<any>;
    onChange: (perPage: number) => void;
}) {
    const [perPage, setPerPage] = useState<number>(paginator.per_page);
    const { isOpen } = useDropdown();

    useEffect(() => {
        onChange(perPage);
    }, [perPage]);

    return (
        <Dropdown
            button={
                <div className="w-full">
                    <div className="flex items-center transition-default">
                        <div className={classNames({
                            'flex justify-center items-center py-2 px-3 space-x-2 text-sm font-semibold leading-4 transition-default': true,
                            'dark:text-theme-dark-50': disabled === false,
                            'dark:bg-theme-dark-800': disabled === true,
                        })}>
                            <span>{perPage}</span>

                            <span
                                className={classNames({
                                    "transition-default": true,
                                    'rotate-180': isOpen,
                                })}
                            >
                                <ChevronDownSmall
                                    className="w-3 h-3"
                                />
                            </span>
                        </div>
                    </div>
                </div>
            }
        >
            <DropdownItem
                onClick={() => setPerPage(10)}
                selected={perPage === 10}
            >
                10
            </DropdownItem>

            <DropdownItem
                onClick={() => setPerPage(25)}
                selected={perPage === 25}
            >
                25
            </DropdownItem>

            <DropdownItem
                onClick={() => setPerPage(50)}
                selected={perPage === 50}
            >
                50
            </DropdownItem>

            <DropdownItem
                onClick={() => setPerPage(100)}
                selected={perPage === 100}
            >
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
            <PerPageComponent
                disabled={disabled}
                paginator={paginator}
                onChange={onChange}
            />
        </DropdownProvider>
    );
}
