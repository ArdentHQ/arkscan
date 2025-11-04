import Pagination from "@/Components/Tables/Pagination/Pagination";
import { useConfig } from "@/Providers/Config/ConfigContext";
import { IPaginatedResponse } from "@/types";
import classNames from "@/utils/class-names";
import React, { useRef } from "react";
import { useTranslation } from "react-i18next";

export function TableHeaderWrapper({
    breakpoint = "sm",
    resultCount,
    resultSuffix,
    children,
}: {
    breakpoint: "sm" | "md" | "none";
    resultCount: number;
    resultSuffix?: string;
    children?: React.ReactNode;
}) {
    const { t } = useTranslation();

    let breakpointClass =
        {
            none: "flex-row justify-between items-center space-y-0",
            sm: "sm:flex-row sm:justify-between sm:items-center sm:space-y-0",
            md: "md:flex-row md:justify-between md:items-center md:space-y-0",
        }[breakpoint] ?? "sm:flex-row sm:justify-between sm:items-center sm:space-y-0";

    if (breakpoint !== "none") {
        breakpointClass = classNames({
            [breakpointClass]: true,
            "flex-col space-y-3": true,
        });
    }

    return (
        <div
            data-test-id="table-header"
            className={classNames({
                "flex md:rounded-t-xl md:border md:border-b-0 md:border-theme-secondary-300 md:px-6 md:dark:border-theme-dark-700": true,
                "pb-4 md:pt-4": children !== undefined,
                "pb-5 md:pt-5": children === undefined,
                [breakpointClass]: true,
            })}
        >
            <div className="font-semibold dark:text-theme-dark-200">
                <span className="hidden sm:inline">
                    {t("pagination.showing_x_results", {
                        results: resultCount.toLocaleString("us", { maximumFractionDigits: 0 }),
                    })}
                </span>

                <span className="sm:hidden">
                    {t("pagination.x_results", {
                        results: resultCount.toLocaleString("us", { maximumFractionDigits: 0 }),
                    })}
                </span>

                {resultSuffix !== undefined && <span>{resultSuffix}</span>}
            </div>

            {children !== undefined && <div>{children}</div>}
        </div>
    );
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
    headerActions,
    noResultsMessage,
}: {
    columns: React.ReactNode;
    withHeader?: boolean;
    withFooter?: boolean;
    resultCount?: number;
    resultSuffix?: string;
    paginator: IPaginatedResponse<any>;
    rowComponent: React.ComponentType<{ row: any; key?: React.Key }>;
    mobile?: React.ReactNode;
    headerActions?: React.ReactNode;
    noResultsMessage?: React.ReactNode;
}) {
    const tableRef = useRef<HTMLDivElement>(null);
    const { pagination } = useConfig();

    return (
        <div ref={tableRef} className="px-6 pb-8 pt-6 md:mx-auto md:max-w-7xl md:px-10 md:pt-0">
            {withHeader && (
                <TableHeaderWrapper resultCount={resultCount} resultSuffix={resultSuffix} breakpoint="md">
                    {headerActions}
                </TableHeaderWrapper>
            )}

            <div
                className={classNames({
                    "hidden w-full overflow-hidden border border-theme-secondary-300 dark:border-theme-dark-700 md:block": true,
                    "rounded-t-xl": !withHeader,
                    "rounded-b-xl": !withFooter || resultCount === 0,
                })}
            >
                <div className="table-container table-encapsulated encapsulated-table-header-gradient px-6">
                    <table>
                        <thead>
                            <tr className="text-sm">{columns}</tr>
                        </thead>

                        <tbody>
                            {paginator?.data &&
                                paginator.data.map((row, index) =>
                                    React.createElement(rowComponent, { key: index, row }),
                                )}
                        </tbody>
                    </table>

                    {resultCount === 0 && <div className="px-6 py-4 text-center">{noResultsMessage}</div>}

                    {!!pagination && resultCount < pagination?.per_page && (
                        <div className="-mx-6 h-[5px] bg-theme-secondary-300 dark:bg-theme-dark-700"></div>
                    )}
                </div>
            </div>

            {mobile}

            {withFooter && paginator && resultCount > 0 && <Pagination paginator={paginator} tableRef={tableRef} />}
        </div>
    );
}
