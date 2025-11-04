import LoadingText from "@/Components/Loading/Text";
import TableCell from "./TableCell";
import classNames from "@/utils/class-names";
import TableHeader, { TableHeaderTooltip } from "./TableHeader";
import { IPaginatedResponse } from "@/types";
import Pagination from "../Pagination/Pagination";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";

export interface ILoadingTableColumn {
    name?: string;
    width?: number;
    indicatorHeight?: string;
    indicatorWidth?: string;
    type?: "id" | "number" | "badge" | "string" | "address";
    className?: string;
    tooltip?: string;
    responsive?: boolean;
    breakpoint?: "sm" | "md" | "md-lg" | "lg" | "xl";
    lastOn?: "sm" | "md" | "md-lg" | "lg" | "xl";
}

export function LoadingTableWrapper({
    columns,
    rowCount,
    indicatorHeight = "h-[17px]",
    withPagination = false,
}: {
    columns: Array<ILoadingTableColumn>;
    rowCount: number;
    indicatorHeight?: string;
    withPagination?: boolean;
}) {
    return (
        <div
            className={classNames({
                "hidden px-6 pt-6 md:mx-auto md:block md:max-w-7xl md:px-10 md:pt-0": true,
                "pb-8": !withPagination,
            })}
        >
            <div
                className={classNames({
                    "validator-monitor hidden w-full overflow-hidden rounded-t-xl border border-theme-secondary-300 dark:border-theme-dark-700 md:block":
                        true,
                    "rounded-b-xl": !withPagination,
                })}
            >
                <div className="table-container table-encapsulated encapsulated-table-header-gradient px-6">
                    <table>
                        <thead>
                            <tr>
                                {columns.map((column, index) => (
                                    <TableHeader
                                        key={index}
                                        className={classNames({
                                            "whitespace-nowrap": true,
                                            "text-right": !!column?.type && ["number"].includes(column?.type),
                                            [column.className as string]: column.className !== undefined,
                                        })}
                                        lastOn={column.lastOn}
                                        responsive={column.responsive}
                                        breakpoint={column.breakpoint}
                                    >
                                        {!column.tooltip && (column.name || "")}

                                        {!!column.tooltip && (
                                            <div className="inline-flex items-center space-x-2">
                                                {column.name && <span>{column.name}</span>}

                                                <TableHeaderTooltip text={column.tooltip} />
                                            </div>
                                        )}
                                    </TableHeader>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {Array.from({ length: rowCount }).map((_, index) => (
                                <tr key={index}>
                                    {columns.map((column, colIndex) => (
                                        <TableCell
                                            key={colIndex}
                                            style={{ width: column.width }}
                                            className={classNames({
                                                "whitespace-nowrap": true,
                                                "text-right": !!column?.type && ["number"].includes(column?.type),
                                                [column.className as string]: column.className !== undefined,
                                            })}
                                            lastOn={column.lastOn}
                                            responsive={column.responsive}
                                            breakpoint={column.breakpoint}
                                        >
                                            {column?.type && ["number"].includes(column?.type) && (
                                                <LoadingText
                                                    width={column.indicatorWidth || "w-[70px]"}
                                                    height={column.indicatorHeight || indicatorHeight}
                                                />
                                            )}

                                            {column?.type && ["id"].includes(column?.type) && (
                                                <LoadingText
                                                    width={column.indicatorWidth || "w-[20px]"}
                                                    height={column.indicatorHeight || indicatorHeight}
                                                />
                                            )}

                                            {column?.type && ["badge"].includes(column?.type) && (
                                                <LoadingText
                                                    width={column.indicatorWidth || "w-[140px]"}
                                                    height={column.indicatorHeight || indicatorHeight}
                                                />
                                            )}

                                            {column?.type && ["address"].includes(column?.type) && (
                                                <div className="flex space-x-2">
                                                    <LoadingText
                                                        width={column.indicatorWidth || "w-[39px]"}
                                                        height={column.indicatorHeight || indicatorHeight}
                                                    />

                                                    <LoadingText
                                                        width={column.indicatorWidth || "w-[70px]"}
                                                        height={column.indicatorHeight || indicatorHeight}
                                                    />
                                                </div>
                                            )}

                                            {(!column?.type ||
                                                ["number", "id", "badge", "address"].includes(column?.type) ===
                                                    false) && (
                                                <LoadingText height={column.indicatorHeight || indicatorHeight} />
                                            )}
                                        </TableCell>
                                    ))}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}

export default function LoadingTable({
    columns,
    rowCount,
    mobile,
    paginator,
    indicatorHeight = "h-[17px]",
}: {
    columns: Array<ILoadingTableColumn>;
    rowCount: number;
    mobile?: React.ReactNode;
    paginator?: IPaginatedResponse<any>;
    indicatorHeight?: string;
}) {
    let isLoading = false;
    if (paginator) {
        isLoading = usePageHandler().isLoading;
    }

    return (
        <>
            <LoadingTableWrapper
                withPagination={isLoading}
                rowCount={rowCount}
                columns={columns}
                indicatorHeight={indicatorHeight}
            />

            {!!mobile && <div className="px-6 md:hidden md:px-10">{mobile}</div>}

            {isLoading && paginator && (paginator?.total ?? 0) > 0 && (
                <div className="px-6 pt-6 md:mx-auto md:max-w-7xl md:px-10 md:pt-0">
                    <Pagination paginator={paginator} />
                </div>
            )}
        </>
    );
}
