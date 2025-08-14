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


// @props([
//     'responsive' => false,
//     'breakpoint' => 'lg',
//     // In wich screen sizes this column will be the first one  (`xl`, `lg`, etc)
//     // (Only neccesary if the first column changes on responsive versions)
//     'firstOn' => null,
//     // In wich screen sizes this column will be the last one (`xl`, `lg`, etc)
//     // (Only neccesary if the last column changes on responsive versions)
//     'lastOn' => null,
//     'class' => '',
//     'colspan' => null,
// ])

// <td {{ $attributes->merge([
//     'class' =>
//         'hoverable-cell'
//         . ($responsive && !$breakpoint ? ' hidden lg:table-cell' : '')
//         . ($responsive && $breakpoint === 'xl' ? ' hidden xl:table-cell' : '')
//         . ($responsive && $breakpoint === 'lg' ? ' hidden lg:table-cell' : '')
//         . ($responsive && $breakpoint === 'md-lg' ? ' hidden md-lg:table-cell' : '')
//         . ($responsive && $breakpoint === 'md' ? ' hidden md:table-cell' : '')
//         . ($responsive && $breakpoint === 'sm' ? ' hidden sm:table-cell' : '')
//         . ($lastOn === 'sm' ? ' last-cell last-cell-sm' : '')
//         . ($lastOn === 'md' ? ' last-cell last-cell-md' : '')
//         . ($lastOn === 'md-lg' ? ' last-cell last-cell-md-lg' : '')
//         . ($lastOn === 'lg' ? ' last-cell last-cell-lg' : '')
//         . ($lastOn === 'xl' ? ' last-cell last-cell-xl' : '')
//         . ($firstOn === 'sm' ? ' first-cell first-cell-sm' : '')
//         . ($firstOn === 'md' ? ' first-cell first-cell-md' : '')
//         . ($firstOn === 'md-lg' ? ' first-cell first-cell-md-lg' : '')
//         . ($firstOn === 'lg' ? ' first-cell first-cell-lg' : '')
//         . ($firstOn === 'xl' ? ' first-cell first-cell-xl' : '')
//         . ' ' . $class
// ]) }}
//     @if ($colspan) colspan="{{ $colspan }}" @endif
// >
//     <div class="table-cell-bg"></div>
//     <div class="table-cell-content">
//         {{ $slot }}
//     </div>
// </td>
