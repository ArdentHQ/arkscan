import classNames from "@/utils/class-names";

export default function TableCell({
    responsive = false,
    breakpoint = 'lg',
    firstOn = null,
    lastOn = null,
    className = '',
    colspan = null,
    children,

    ...props
}: React.TdHTMLAttributes<HTMLTableCellElement> & React.PropsWithChildren<{
    responsive?: boolean;
    breakpoint?: 'xl' | 'lg' | 'md-lg' | 'md' | 'sm';
    firstOn?: 'xl' | 'lg' | 'md-lg' | 'md' | 'sm' | null;
    lastOn?: 'xl' | 'lg' | 'md-lg' | 'md' | 'sm' | null;
    className?: string;
    colspan?: number | null;
}>) {
    return (
        <td
            {...props}
            className={classNames({
                "hoverable-cell": true,
                "hidden lg:table-cell": responsive && ! breakpoint,
                [`hidden ${breakpoint}:table-cell`]: responsive && !! breakpoint,
                [`last-cell last-cell-${lastOn}`]: !! lastOn,
                [`first-cell first-cell-${firstOn}`]: !! firstOn,
                [className]: true,
            })}
            colSpan={colspan || undefined}
        >
            <div className="table-cell-bg"></div>
            <div className="table-cell-content">
                {children}
            </div>
        </td>
    );
}
