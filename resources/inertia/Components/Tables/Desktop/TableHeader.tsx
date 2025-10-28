import Info from "@/Components/General/Info";
import classNames from "@/utils/class-names";
import { useTranslation } from "react-i18next";

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
    }>) {
    const { t } = useTranslation();

    return (
        <th
            {...props}
            className={classNames({
                "hidden lg:table-cell": responsive && !breakpoint,
                "text-right": type === "number",
                "w-[40px]": type === "id",
                [`hidden ${breakpoint}:table-cell`]: responsive && !!breakpoint,
                [`last-cell last-cell-${lastOn}`]: !!lastOn && lastOn !== "full",
                [`last-cell`]: lastOn === "full",
                [`first-cell first-cell-${firstOn}`]: !!firstOn,
                [className]: true,
            })}
        >
            {!tooltip && (name ? t(name) : children)}

            {!!tooltip && (
                <div className="inline-flex items-center space-x-2">
                    <span>{name ? t(name) : children}</span>

                    <TableHeaderTooltip text={tooltip} />
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
