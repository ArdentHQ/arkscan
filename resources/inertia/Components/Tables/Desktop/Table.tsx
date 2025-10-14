import Pagination from "@/Components/Tables/Pagination/Pagination";
import { IPaginatedResponse } from "@/types";
import classNames from "@/utils/class-names";
import React, { useRef } from "react";
import { useTranslation } from "react-i18next";

function TableHeader({ breakpoint = "sm", resultCount, resultSuffix, children }: { breakpoint: "sm" | "md" | "none"; resultCount: number; resultSuffix?: string; children?: React.ReactNode }) {
    const { t } = useTranslation();

    let breakpointClass = {
        "none": 'flex-row justify-between items-center space-y-0',
        'sm': 'sm:flex-row sm:justify-between sm:items-center sm:space-y-0',
        'md': 'md:flex-row md:justify-between md:items-center md:space-y-0',
    }[breakpoint] ?? 'sm:flex-row sm:justify-between sm:items-center sm:space-y-0';

    if (breakpoint !== "none") {
        breakpointClass = classNames({
            [breakpointClass]: true,
            'flex-col space-y-3': true,
        });
    }

    return (
        <div className={classNames({
            "flex md:px-6 md:rounded-t-xl md:border md:border-b-0 md:border-theme-secondary-300 md:dark:border-theme-dark-700": true,
            "pb-4 md:pt-4": children !== undefined,
            "pb-5 md:pt-5": children === undefined,
            [breakpointClass]: true,
        })}>
            <div className="font-semibold dark:text-theme-dark-200">
                <span className="hidden sm:inline">
                    {t('pagination.showing_x_results', { results: resultCount.toLocaleString('us', {maximumFractionDigits: 0}) })}
                </span>

                <span className="sm:hidden">
                    {t('pagination.x_results', { results: resultCount.toLocaleString('us', {maximumFractionDigits: 0}) })}
                </span>

                {resultSuffix !== undefined && <span>{resultSuffix}</span>}
            </div>

            {children !== undefined && (
                <div>
                    {children}
                </div>
            )}
        </div>
    )
}

export function Table({
    columns,
    withHeader = false,
    withFooter = false,
    resultCount = 0,
    resultSuffix,
    paginator,
    rowComponent,
    mobile,
}: {
    columns: React.ReactNode;
    withHeader?: boolean;
    withFooter?: boolean;
    resultCount?: number;
    resultSuffix?: string;
    paginator: IPaginatedResponse<any>;
    rowComponent: React.ComponentType<{ row: any; key?: React.Key }>;
    mobile?: React.ReactNode;
}) {
    const tableRef = useRef<HTMLDivElement>(null);

    return (
        <div
            ref={tableRef}
            className="px-6 pt-6 pb-8 md:px-10 md:pt-0 md:mx-auto md:max-w-7xl"
        >
            {withHeader && (
                <TableHeader
                    resultCount={resultCount}
                    resultSuffix={resultSuffix}
                    breakpoint="md"
                />
            )}

            <div className={classNames({
                "border border-theme-secondary-300 dark:border-theme-dark-700 overflow-hidden hidden w-full md:block": true,
                "rounded-t-xl": ! withHeader,
                "rounded-b-xl": ! withFooter,
            })}>
                <div className="px-6 table-container table-encapsulated encapsulated-table-header-gradient">
                    <table>
                        <thead>
                            <tr className="text-sm">
                                {columns}
                            </tr>
                        </thead>

                        <tbody>
                            {paginator?.data && paginator.data.map((row, index) => (
                                React.createElement(rowComponent, { key: index, row })
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            {mobile}

            {withFooter && paginator && (
                <Pagination
                    paginator={paginator}
                    tableRef={tableRef}
                />
            )}
        </div>
    );
}
