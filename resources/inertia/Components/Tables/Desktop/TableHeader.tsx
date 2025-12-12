import Info from "@/Components/General/Info";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { SortDirection } from "@/types/generated";
import classNames from "classnames";
import { useTranslation } from "react-i18next";
import CaretUpIcon from "@ui/icons/arrows/caret-up.svg?react";
import CaretDownIcon from "@ui/icons/arrows/caret-down.svg?react";
import { useTableSorting } from "@/Providers/TableSorting/TableSortingContext";

function SortIcon({
    sortId,
    sortBy,
    sortDirection = SortDirection.ASC,
    disabled,
}: {
    sortId: string;
    sortBy?: string;
    sortDirection?: SortDirection;
    disabled?: boolean;
}) {
    return (
        <div
            className={classNames([
                "flex flex-col text-theme-secondary-500 dark:text-theme-dark-500",
                !disabled && "group-hover/header:text-theme-secondary-900 group-hover/header:dark:text-theme-dark-50",
            ])}
        >
            <div
                className={classNames([
                    "transition-default",
                    sortBy === sortId &&
                        sortDirection === SortDirection.ASC &&
                        "text-theme-primary-600 dark:text-theme-dark-blue-400",
                ])}
            >
                <CaretUpIcon className="h-2 w-2" />
            </div>

            <div
                className={classNames([
                    "transition-default",
                    sortBy === sortId &&
                        sortDirection === SortDirection.DESC &&
                        "text-theme-primary-600 dark:text-theme-dark-blue-400",
                    sortBy !== sortId && "text-theme-secondary-500 dark:text-theme-dark-500",
                ])}
            >
                <CaretDownIcon className="h-2 w-2" />
            </div>
        </div>
    );
}

function SortableHeader({
    sortId,
    placement = "right",
    disabled,
    children,
    type,
}: {
    sortId: string;
    placement?: "left" | "right";
    disabled?: boolean;
    children: React.ReactNode;
    type?: "string" | "number" | "id";
}) {
    const { isLoading } = usePageHandler();
    const { sortBy, sortDirection, sort } = useTableSorting();

    return (
        <button
            type="button"
            data-testid={`table:header:sortable:${sortId}`}
            className={classNames([
                "-my-3 flex w-full items-center space-x-2 py-3",
                type === "number" && "justify-end",
            ])}
            disabled={disabled || isLoading}
            onClick={() => {
                if (disabled) {
                    return;
                }

                sort(sortId);
            }}
        >
            {placement === "left" && (
                <SortIcon sortId={sortId} sortBy={sortBy} sortDirection={sortDirection} disabled={disabled} />
            )}

            {children}

            {placement === "right" && (
                <SortIcon sortId={sortId} sortBy={sortBy} sortDirection={sortDirection} disabled={disabled} />
            )}
        </button>
    );
}

export default function TableHeader({
    responsive = false,
    breakpoint = "lg",
    firstOn,
    lastOn,
    className = "",
    name,
    children,
    type,
    tooltip,
    sortId,

    ...props
}: React.TdHTMLAttributes<HTMLTableCellElement> &
    React.PropsWithChildren<{
        responsive?: boolean;
        breakpoint?: "xl" | "lg" | "md-lg" | "md" | "sm";
        firstOn?: "xl" | "lg" | "md-lg" | "md" | "sm";
        lastOn?: "xl" | "lg" | "md-lg" | "md" | "sm" | "full";
        className?: string;
        name?: string;
        type?: "string" | "number" | "id";
        tooltip?: string;
        sortId?: string;
    }>) {
    const { t } = useTranslation();

    return (
        <th
            {...props}
            className={classNames({
                "group/header": true,
                "hidden lg:table-cell": responsive && !breakpoint,
                "text-right": !sortId && type === "number",
                "w-[40px]": type === "id",
                [`hidden ${breakpoint}:table-cell`]: responsive && !!breakpoint,
                [`last-cell last-cell-${lastOn}`]: !!lastOn && lastOn !== "full",
                [`last-cell`]: lastOn === "full",
                [`first-cell first-cell-${firstOn}`]: !!firstOn,
                [className]: true,
            })}
        >
            {!tooltip && (
                <>
                    {sortId ? (
                        <SortableHeader sortId={sortId} disabled={!sortId} type={type}>
                            <span>{name ? t(name) : children}</span>
                        </SortableHeader>
                    ) : name ? (
                        t(name)
                    ) : (
                        children
                    )}
                </>
            )}

            {!!tooltip && (
                <div className={classNames(["flex items-center space-x-2", type === "number" && "justify-end"])}>
                    {sortId ? (
                        <SortableHeader sortId={sortId} disabled={!sortId} type={type}>
                            <span>{name ? t(name) : children}</span>

                            <TableHeaderTooltip text={tooltip} />
                        </SortableHeader>
                    ) : (
                        <>
                            <span>{name ? t(name) : children}</span>

                            <TableHeaderTooltip text={tooltip} />
                        </>
                    )}
                </div>
            )}
        </th>
    );
}

export function TableHeaderTooltip({ text, type = "info" }: { text: string; type?: "question" | "info" }) {
    return (
        <div className="ark-info-element flex h-5 w-5 justify-end">
            <Info type={type} tooltip={text} className="ml-1 inline-block" />
        </div>
    );
}
