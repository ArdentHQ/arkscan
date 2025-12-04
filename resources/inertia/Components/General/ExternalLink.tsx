import ArrowExternalIcon from "@ui/icons/arrows/arrow-external.svg?react";

export default function ExternalLink({
    url,
    className = "link font-semibold inline break-words",
    innerClass = "",
    noIcon = false,
    iconClass = "inline relative flex-shrink-0 ml-0.5 text-theme-secondary-500 w-3 h-3",
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
        <a href={url} className={className} target="_blank" rel="noopener nofollow noreferrer">
            <div className="flex items-center justify-center space-x-2">
                <span className={innerClass}>{children}</span>

                {!noIcon && <ArrowExternalIcon className={iconClass} />}
            </div>
        </a>
    );
}
