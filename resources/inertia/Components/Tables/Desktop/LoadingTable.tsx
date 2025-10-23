import LoadingText from "@/Components/Loading/Text";
import TableCell from "./TableCell";
import classNames from "@/utils/class-names";

export default function LoadingTable({
    columns,
    rowCount,
    indicatorHeight = "h-[17px]",
}: {
    columns: Array<{
        name?: string;
        width?: number;
        indicatorHeight?: string;
        type?: 'id' | 'number' | 'badge' | 'string' | 'address';
        className?: string;
    }>;
    rowCount: number;
    indicatorHeight?: string;
}) {
    return (
        <div className="hidden md:block px-6 pt-6 pb-8 md:px-10 md:pt-0 md:mx-auto md:max-w-7xl">
            <div className="border border-theme-secondary-300 dark:border-theme-dark-700 overflow-hidden rounded-t-xl rounded-b-xl hidden w-full md:block validator-monitor">
                <div className="px-6 table-container table-encapsulated encapsulated-table-header-gradient">
                    <table>
                        <thead>
                            <tr>
                                {columns.map((column, index) => (
                                    <th
                                        key={index}
                                        className={classNames({
                                            "whitespace-nowrap": true,
                                            [column.className as string]: column.className !== undefined,
                                        })}
                                    >
                                        {column.name || ''}
                                    </th>
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
                                                [column.className as string]: column.className !== undefined,
                                            })}
                                        >
                                            {column?.type && ['number'].includes(column?.type) && <LoadingText width="w-[32px]" height={column.indicatorHeight || indicatorHeight} />}
                                            {column?.type && ['id'].includes(column?.type) && <LoadingText width="w-[20px]" height={column.indicatorHeight || indicatorHeight} />}
                                            {column?.type && ['badge'].includes(column?.type) && <LoadingText width="w-[140px]" height={column.indicatorHeight || indicatorHeight} />}
                                            {column?.type && ['address'].includes(column?.type) && (
                                                <div className="flex space-x-2">
                                                    <LoadingText width="w-[39px]" height={column.indicatorHeight || indicatorHeight} />

                                                    <LoadingText width="w-[70px]" height={column.indicatorHeight || indicatorHeight} />
                                                </div>
                                            )}

                                            {(! column?.type || ['number', 'id', 'badge', 'address'].includes(column?.type) === false) && <LoadingText height={column.indicatorHeight || indicatorHeight} />}
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
