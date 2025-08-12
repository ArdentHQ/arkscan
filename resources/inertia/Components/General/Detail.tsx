import classNames from "@/utils/class-names";
import LoadingText from "../Loading/Text";

export default function Detail({
    title,
    titleClass = '',
    value = null,
    isLoading = false,
    className = '',
    children,
}: React.PropsWithChildren<{
    title: string;
    titleClass?: string;
    value?: string | number | null;
    isLoading?: boolean;
    className?: string;
}>) {
    return (
        <div className="flex flex-col space-y-2 font-semibold">
            <div className={classNames({
                "text-sm whitespace-nowrap text-theme-secondary-700 dark:text-theme-dark-200": true,
                [titleClass]: true,
            })}>
                {title}
            </div>

            <div className={classNames({
                "text-theme-secondary-900 dark:text-theme-dark-50 leading-5": true,
                [className]: true,
            })}>
                {isLoading && <LoadingText height="h-5" />}
                {! isLoading && !! value && <>
                    {value}
                </>}
                {! isLoading && ! value && <>
                    {children}
                </>}
            </div>
        </div>
    );
}

// <div class="flex flex-col space-y-2 font-semibold">
//     <div @class([
//         'text-sm whitespace-nowrap text-theme-secondary-700 dark:text-theme-dark-200',
//         $titleClass,
//     ])>
//         {{ $title }}
//     </div>

//     <div {{ $attributes->class('text-theme-secondary-900 dark:text-theme-dark-50 leading-5') }}>
//         @if ($loading)
//             <x-loading.text height="h-5" />
//         @elseif ($value)
//             {{ $value }}
//         @else
//             {{ $slot }}
//         @endif
//     </div>
// </div>
