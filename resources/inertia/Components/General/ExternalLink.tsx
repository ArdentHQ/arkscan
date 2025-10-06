import ArrowExternal from "@/Assets/Icons/Arrows/ArrowExternal";

export default function ExternalLink({
    url,
    className = 'link font-semibold inline break-words',
    innerClass = '',
    noIcon = false,
    iconClass = 'inline relative -top-1 flex-shrink-0 mt-1 ml-0.5 text-theme-secondary-500 w-3 h-3',
    children,
}: {
    url: string;
    className?: string;
    innerClass?: string;
    noIcon?: boolean;
    iconClass?: string;
    children?: React.ReactNode;
}) {
    return (
        <a
            href={url}
            className={className}
            target="_blank"
            rel="noopener nofollow noreferrer"
        >
            <div className="flex space-x-1 items-center justify-center">
                <span className={innerClass}>
                    {children}
                </span>

                {! noIcon && (
                    <span>
                        <ArrowExternal className={iconClass} />
                    </span>
                )}
            </div>
        </a>
    );
}
