import LoadingText from "@/Components/Loading/Text";
import TableCell from "./TableCell";

export default function LoadingTable({ columns, rowCount }: {
    columns: Array<{
        name?: string;
        width?: number;
        type?: 'id' | 'number' | 'badge' | 'text';
        className?: string;
    }>;
    rowCount: number;
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
                                        style={{ width: column.width }}
                                        className={column.className}
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
                                            className={column.className}
                                        >
                                            {column?.type && ['number'].includes(column?.type) && <LoadingText width="w-[32px]" height="h-[20px]" />}
                                            {column?.type && ['id'].includes(column?.type) && <LoadingText width="w-[20px]" height="h-[20px]" />}
                                            {column?.type && ['badge'].includes(column?.type) && <LoadingText width="w-[140px]" height="h-[20px]" />}
                                            {(! column?.type || ['number', 'id', 'badge'].includes(column?.type) === false) && <LoadingText height="h-[20px]" />}
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
