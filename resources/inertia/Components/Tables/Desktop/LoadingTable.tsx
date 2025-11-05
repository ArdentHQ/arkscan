import LoadingText from "@/Components/Loading/Text";
import TableCell from "./TableCell";
import classNames from "@/utils/class-names";
import TableHeader, { TableHeaderTooltip } from "./TableHeader";
import { TableHeaderWrapper } from "./Table";

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

export default function LoadingTable({
    columns,
    rowCount,
    indicatorHeight = "h-[17px]",
    header,
}: {
    columns: Array<ILoadingTableColumn>;
    rowCount: number;
    indicatorHeight?: string;
    header?: React.ReactNode;
}) {
    return (
        <div className="hidden px-6 pb-8 pt-6 md:mx-auto md:block md:max-w-7xl md:px-10 md:pt-0">
            {!!header && (
                <TableHeaderWrapper resultCount={0} breakpoint="md">
                    {header}
                </TableHeaderWrapper>
            )}

            <div
                className={classNames({
                    "validator-monitor hidden w-full overflow-hidden rounded-b-xl border border-theme-secondary-300 dark:border-theme-dark-700 md:block": true,
                    "rounded-t-xl": !header,
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
