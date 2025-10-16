import classNames from "@/utils/class-names";
import { useTranslation } from "react-i18next";

export default function TableCell({
    responsive = false,
    breakpoint = 'lg',
    firstOn,
    lastOn,
    className = '',
    name,
    children,

    ...props
}: React.TdHTMLAttributes<HTMLTableCellElement> & React.PropsWithChildren<{
    responsive?: boolean;
    breakpoint?: 'xl' | 'lg' | 'md-lg' | 'md' | 'sm';
    firstOn?: 'xl' | 'lg' | 'md-lg' | 'md' | 'sm';
    lastOn?: 'xl' | 'lg' | 'md-lg' | 'md' | 'sm' | 'full';
    className?: string;
    name?: string;
}>) {
    const { t } = useTranslation();

    return (
        <th
            {...props}
            className={classNames({
                "hidden lg:table-cell": responsive && ! breakpoint,
                [`hidden ${breakpoint}:table-cell`]: responsive && !! breakpoint,
                [`last-cell last-cell-${lastOn}`]: !! lastOn && lastOn !== 'full',
                [`last-cell`]: lastOn === 'full',
                [`first-cell first-cell-${firstOn}`]: !! firstOn,
                [className]: true,
            })}
        >
            {name ? t(name) : children}
        </th>
    );
}
